<?php

/**
 * Class FV_Public_Social_Login
 * @since 2.3.00
 */
class FV_Public_Social_Login extends FV_Singleton_Abstract {

    protected static $instance = null;

    /**
     * Ajax :: save into session social login data
     * @param #contest_id
     * @param #some_str
     * @param #access_token
     */
    public function AJAX_login() {

        $valid_data = fv_params_validate($_POST, [
            'contest_id'      => 'required|integer',
            'soc_network'     => 'required',
            'some_str'        => 'required',
        ]);

        if ( false === wp_verify_nonce($valid_data['some_str'], 'fv_vote') ) {
            fv_AJAX_response( false, fv_get_transl_msg('invalid_token') );
        }
        
        $contest_id = (int)$valid_data['contest_id'];

        if (session_id() == '') {
            session_start();
        }

        if ( $this->_have_login_in_session() ) {
            fv_AJAX_response( true );
        }

        switch ($valid_data['soc_network']) {
            case 'facebook':
                $this->fb_login( $contest_id);
                break;
            case 'vk':
                $this->vk_login( $contest_id);
                break;
            case 'google':
                $this->gp_login( $contest_id);
                break;
            default:
                fv_AJAX_response( false, 'Invalid network!' );
        }


        session_write_close();

        fv_AJAX_response( true );
    }
    
    public function fb_login( $contest_id ) {

        if ( !get_option('fotov-fb-apikey') || ! get_option('fv-fb-secret') ) {
            fv_AJAX_response( false, 'Facebook API keys are missing!' );
        }

        $access_token = !empty($_POST['data']) ? sanitize_text_field($_POST['data']) : '';

        if ( !$access_token ) {
            fv_AJAX_response( false, 'Facebook login error!' );
        }

        try {
            $fb = new \Facebook\Facebook([
                'app_id' => get_option('fotov-fb-apikey'),
                'app_secret' => get_option('fv-fb-secret'),
                //'default_graph_version' => 'v2.70',
                'default_access_token' => $access_token, // optional
                'http_client_handler' => 'stream',    // Since 2.3.06, for for a Video contest addon
                //'http_client_handler' => 'guzzle',
            ]);

            // Use one of the helper classes to get a Facebook\Authentication\AccessToken entity.
            //   $helper = $fb->getRedirectLoginHelper();
            $helper = $fb->getJavaScriptHelper();


            $accessToken = $helper->getAccessToken();

            $response = $fb->get('/me?fields=name,email,age_range');

            $me = $response->getGraphUser();
//            echo 'Logged in as ' . $me->getName() . PHP_EOL;
//            echo 'Logged in as ' . $me->getLink() . PHP_EOL;
//            echo 'Logged in as ' . $me->getEmail() . PHP_EOL;
//            echo 'Logged in as ' . $me->getId() . PHP_EOL;
//            echo 'Logged in as ' . $me->getBirthday() . PHP_EOL;
//            echo 'Logged in as ' . $me->getBirthday() . PHP_EOL;


            $user_data['email'] = sanitize_email( $me->getEmail() );
            $user_data['soc_name'] = sanitize_text_field( $me->getName() );

            $user_data['soc_profile'] = 'https://www.facebook.com/app_scoped_user_id/' . absint( $me->getId() );
            $user_data['soc_network'] = 'facebook';
            $user_data['soc_uid'] = absint( $me->getId() );

            $this->_save_login_to_session( $user_data );
            $this->_save_login_to_subscribers( $contest_id, $user_data );

        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            fv_log( 'Simple Social login: FB Graph returned an error: ' . $e->getMessage() );
            fv_AJAX_response( false, 'FB Graph returned an error' );
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            fv_log( 'Simple Social login: Facebook SDK returned an error: ' . $e->getMessage() );
            fv_AJAX_response( false, 'Facebook SDK returned an error' );
        }
    }

    /**
     * @param $contest_id
     *
     * @see https://vk.com/dev/openapi?f=3.4.%2BVK.Auth.getLoginStatus
     */
    public function vk_login( $contest_id ) {

        $app_id = fv_setting('vk-app-id');
        $app_secret = fv_setting('vk-app-secret');

        if ( !$app_id || ! $app_secret ) {
            fv_AJAX_response( false, 'VK API keys are missing!' );
        }

        if ( ! $this->_vk_verify_auth( $app_id, $app_secret ) ) {
            fv_AJAX_response( false, 'Invalid access token!' );
        }

        $user_data = isset($_POST['data']) && is_array($_POST['data']) ? $_POST['data'] : [];
        
        try {

            $user_data['email'] = '';
            $user_data['soc_name'] = sanitize_text_field( $user_data['first_name'] . ' ' . $user_data['last_name'] );

            $user_data['soc_profile'] = esc_url( $user_data['href'] );
            $user_data['soc_network'] = 'vk';
            $user_data['soc_uid'] = absint( $user_data['id'] );

            $this->_save_login_to_session( $user_data );

        } catch(Exception $e) {
            fv_log( 'Simple Social login: VK generated an error: ' . $e->getMessage() );
            fv_AJAX_response( false, 'Error happens during VK Login!' );
        }

    }

    /**
     * @param $contest_id
     *
     * @see https://developers.google.com/identity/sign-in/web/backend-auth
     */
    public function gp_login( $contest_id ) {

        $app_id = fv_setting('gp-app-id');
        $app_secret = fv_setting('gp-app-secret');

        if ( ! $app_id || ! $app_secret ) {
            fv_AJAX_response( false, 'Google keys are missing!' );
        }

        $access_token = !empty($_POST['data']) ? sanitize_text_field($_POST['data']) : '';

        if ( !$access_token ) {
            fv_AJAX_response( false, 'Google login error!' );
        }

        try {

            $response = wp_remote_get( 'https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=' . $access_token );

            if ( !$response || is_wp_error($response) ) {
                fv_AJAX_response( false, 'Cant\'t verify Sign In Token!' );
            }

            $data = wp_remote_retrieve_body( $response );

            if ( $data ) {
                $data = json_decode( $data, true );
            }

            if ( !$data ) {
                fv_AJAX_response( false, 'Something going wrong!' );
            }

            if ( $app_id !== $data['aud'] ) {
                fv_AJAX_response(false, 'Something going wrong!');
            }


//            {
//                // These six fields are included in all Google ID Tokens.
//                "iss": "https://accounts.google.com",
//                 "sub": "110169484474386276334",
//                 "azp": "1008719970978-hb24n2dstb40o45d4feuo2ukqmcc6381.apps.googleusercontent.com",
//                 "aud": "1008719970978-hb24n2dstb40o45d4feuo2ukqmcc6381.apps.googleusercontent.com",
//                 "iat": "1433978353",
//                 "exp": "1433981953",
//
//                 // These seven fields are only included when the user has granted the "profile" and
//                 // "email" OAuth scopes to the application.
//                 "email": "testuser@gmail.com",
//                 "email_verified": "true",
//                 "name" : "Test User",
//                 "picture": "https://lh4.googleusercontent.com/-kYgzyAWpZzJ/ABCDEFGHI/AAAJKLMNOP/tIXL9Ir44LE/s99-c/photo.jpg",
//                 "given_name": "Test",
//                 "family_name": "User",
//                 "locale": "en"
//                }

            $user_data = $this->_get_empty_data_array();

            if ( $data['email'] ) {
                $user_data['email'] = sanitize_email($data['email']);
            }
            $user_data['soc_name'] = sanitize_text_field( $data['name'] );

            $user_data['soc_network'] = 'google';
            $user_data['soc_uid'] = $data['sub'];

            $this->_save_login_to_session( $user_data );
            $this->_save_login_to_subscribers( $contest_id, $user_data );

        } catch(Exception $e) {
            fv_log( 'Simple Social login: Google generated an error: ' . $e->getMessage() );
            fv_AJAX_response( false, 'Error happens during Google Login!' );
        }

    }

    function _vk_verify_auth( $app_id, $secret ) {
        $session = array();
        $member = FALSE;
        $valid_keys = array('expire', 'mid', 'secret', 'sid', 'sig');
        $app_cookie = $_COOKIE['vk_app_'.$app_id];
        if ($app_cookie) {
            $session_data = explode ('&', $app_cookie, 10);
            foreach ($session_data as $pair) {
                list($key, $value) = explode('=', $pair, 2);
                if (empty($key) || empty($value) || !in_array($key, $valid_keys)) {
                    continue;
                }
                $session[$key] = $value;
            }
            foreach ($valid_keys as $key) {
                if (!isset($session[$key])) return $member;
            }
            ksort($session);

            $sign = '';
            foreach ($session as $key => $value) {
                if ($key != 'sig') {
                    $sign .= ($key.'='.$value);
                }
            }
            $sign .= $secret;
            $sign = md5($sign);
            if ($session['sig'] == $sign && $session['expire'] > time()) {
                $member = array(
                    'id' => intval($session['mid']),
                    'secret' => $session['secret'],
                    'sid' => $session['sid']
                );
            }
        }
        return $member;
    }

    /**
     * @return bool
     */
    public function _have_login_in_session () {
        return !empty($_SESSION['fv_social']);
    }

    /**
     * @param array $data
     */
    public function _save_login_to_session ($data) {
        $social_data = array_merge([
            'email' => '',
            'soc_name' => '',
            'soc_profile' => '',    // Link to profile
            'soc_network' => '',    // FB, VK, etc
            'soc_uid' => '',        // User ID
        ], $data);

        $_SESSION['fv_social'] = $data;
    }

    /**
     * @param int   $contest_id
     * @param array $user_data
     */
    public function _save_login_to_subscribers ( $contest_id, $user_data ) {
        if ( $user_data['email'] ) {
            // find email in database
            $s_count = ModelSubscribers::query()
                ->where_all( array('email'=>$user_data['email'], 'type'=>'social', 'soc_network'=>$user_data['soc_network']) )
                ->find();

            // IF nothing found - fine
            if ( !$s_count ) {
                $to_insert = array(
                    'type'          => 'social',
                    'contest_id'    => $contest_id,
                    'name'          => $user_data['soc_name'],
                    'email'         => $user_data['email'],
                    'user_id'       => get_current_user_id(),
                    'soc_network'   => $user_data['soc_network'],
                );
                ModelSubscribers::query()->insert($to_insert);
            }
        }
    }

    public function _get_empty_data_array() {
        return [
            'email' => '',
            'soc_name' => '',
            'soc_profile' => '',    // Link to profile
            'soc_network' => '',    // FB, VK, etc
            'soc_uid' => '',        // User ID
        ];
    }

}