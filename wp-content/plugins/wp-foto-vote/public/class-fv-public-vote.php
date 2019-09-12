<?php
defined('ABSPATH') or die("No script kiddies please!");

/**
 * The public-facing Voting functionality
 *
 * @since      2.2.073
 *
 * @package    FV
 * @subpackage public
 * @author     Maxim K <support@wp-vote.net>
 */
class FV_Public_Vote {

    public static $vote_debug_var;
    /**
     * @var integer
     */
    public static $contestant_id;
    /**
     * @var integer
     */
    public static $contest_id;
    /**
     * @var integer
     */
    public static $used_votes = 0;
    /**
     * @var  string    $user_country
     * @since 2.2.503
     */
    public static $user_country;
    
    /**
     * Ajax :: Vote for photo
     *
     * @GET-param 'some_str'    WP nonce
     * @GET-param 'user_country' saved in User browser, for decrease queries count to indicate user country by IP
     * @GET-param 'post_id'     post Id
     * @GET-param 'contest_id'
     * @GET-param 'uid'         evercookie identification
     * @GET-param 'fv_name'     if uses contest security `default and Subscribe`
     * @GET-param 'fv_email'    if uses contest security `default and Subscribe`
     * @GET-param 'referer'     from what page user open contest page?
     *
     * @return void
     * @output json_array
     */
    public static function vote()
    {
        //session_start();
        //$my_db = new FV_DB;

        $ip = substr( fv_get_user_ip(), 0, 15 );

        $post_id = (int)$_POST['post_id'];
        if ( $post_id > 99999999 ) {
            $post_id = 1;
        }
        $contest_id = (int)$_POST['contest_id'];
        $vote_id = (int)$_POST['vote_id'];
        self::$contestant_id = $vote_id;
        self::$contest_id = $contest_id;

        $UID = sanitize_text_field($_POST['uid']);
        if ( strlen($UID) > 22 ) {
            $UID = substr($UID, 0 , 22);
        }

        // not allow direct voting
        if ( (!defined('WP_CACHE') || WP_CACHE == FALSE) && !fv_setting('cache-support') ) {
            // IF Invalid WP security token OR passed > 12 hours after it generating
            if ( 1 !== check_ajax_referer('fv_vote', 'some_str', false) ) {
                self::echoVoteRes(98, false, false);     // Wrong WP security token
            }
        }

        $competitor = new FV_Competitor( $vote_id );
        if ( !$competitor ) {
            self::echoVoteRes(99, "Competitor not exists!", false);
        }

        $user_id = get_current_user_id();

        if ( $user_id > 0 && fv_setting('restrict-vote-for-own', false) ) {
            if ( $competitor->user_id == $user_id ) {
                self::echoVoteRes(7, false, false);         // Own photo voting
            }
        }

        if ( ! $competitor->isPublished() ) {
            self::echoVoteRes(9, '', false);
        }

        $CHECK = (isset($_POST['check'])) ? true : false;        // not vote, only check, can user vote?
        // откуда пришёл голосующий
        $referer = '';
        if ( !empty($_POST['rr']) && $_POST['rr'] !== '-' && base64_decode($_POST['rr']) ) {
            $referer = sanitize_text_field(stripcslashes(base64_decode($_POST['rr'])));
        }

        $contest = fv_get_contest($contest_id, true, true);

        $rating = isset($_POST['rating'])? (float)$_POST['rating'] : 0;

        $rating_max = FvFunctions::ss('rate-stars-count', 5);

        /**
         * @since 2.3.03
         */
        if ( $rating > $rating_max ) {
            $rating = $rating_max;
        }

        /**
         * @since 2.3.03
         */
        do_action( 'fv/vote/before_start', $CHECK, $rating, $contest, $vote_id );

        // allow addons change security_type
        $contest->voting_security = apply_filters('fv_vote_contest_security_type', $contest->voting_security, $user_id, $contest->id);

        if ( $contest->voting_security_ext == 'subscribeForNonUsers' && !$user_id ) {
            $contest->voting_security_ext = "subscribe";
        }

        // Detect, need Subscription or not
        if ( in_array($contest->voting_security_ext, array("subscribe", "social") )  )
        {
            if (session_id() == '') {
                session_start();
            }

            $add_subscription = true;
        } else {
            $add_subscription = false;
        }

        if ( $contest->voting_security_ext == 'subscribe' ) {
            if ( !isset($_POST['subscribe_hash']) ||
                    !fv_verify_email_subscribe_hash($_POST['subscribe_hash'], $contest->id, fv_setting('mail-verify', true)) ) {
                if( empty($_SESSION['email_subscribed_'.$contest_id]) ) {
                    self::echoVoteRes('need_subscribe', false, $add_subscription); // need_subscribe
                }
            }
        }

        if ( $user_id == 0 && $contest->limit_by_user != "no" ) {
            self::echoVoteRes(5, false, $add_subscription); // No logged in
        } elseif ( $user_id && $contest->limit_by_user == 'role' && $contest->limit_by_role ){
            $limit_by_role_arr = explode(',' , $contest->limit_by_role);
            $user_meta=get_userdata($user_id);
            $user_roles=$user_meta->roles;
            // If user does not have any of required roles
            if ( !array_intersect($user_roles, $limit_by_role_arr) ) {
                self::echoVoteRes(8, false, $add_subscription); // No required role
            }
        }

        //** Verify ReCaptcha Response
        /*if ( $contest->security_type == "defaultArecaptcha" || $contest->security_type == "cookieArecaptcha" ) {
            if ( isset($_POST['recaptcha_response']) ) {
                $recaptcha_verify = FvFunctions::recaptcha_verify_response($_POST['recaptcha_response'], $ip, fv_setting('recaptcha-secret-key') );
                if ( $recaptcha_verify == false ) {
                    self::echoVoteRes(6, false, $add_subscription);  // wrong reCAPTCHA
                }
            } else {
                fv_log('Vote error - recaptcha_response is empty!', $_POST, __FILE__, __LINE__);
                self::echoVoteRes(99, false, $add_subscription);     // error
            }
        }*/


        //** Verify ReCaptcha Response
        if ( !$CHECK && $contest->voting_security_ext == "reCaptcha" ) {
            $check_recaptcha_response = true;
            // if if enabled solve reCAPTCHA once in 30 minutes and have Session then Check it
            if ( fv_setting('recaptcha-session', false) ) :
                if (session_id() == '') {
                    session_start();
                }
                if ( isset($_SESSION['fv_recaptcha_session']) && (time() - $_SESSION['fv_recaptcha_session']) < 1800 ) {
                    $check_recaptcha_response = false;
                } elseif( !isset($_SESSION['fv_recaptcha_session']) || (time() - $_SESSION['fv_recaptcha_session']) >= 1800 ) {
                    unset($_SESSION['fv_recaptcha_session']);
                }
            ENDIF;

            if ( $check_recaptcha_response ) :
                if ( isset( $_POST['recaptcha_response']) ) {
                    $recaptcha_verify = FvFunctions::recaptcha_verify_response( $_POST['recaptcha_response'], $ip, fv_setting('recaptcha-secret-key') );

                    if ( $recaptcha_verify === 'error' ) {
                        self::echoVoteRes(99, false, $add_subscription);     // error
                    } elseif ( $recaptcha_verify == false ) {
                        self::echoVoteRes(6, false, $add_subscription);  // wrong reCAPTCHA
                    } elseif ( fv_setting('recaptcha-session', false) ) {
                        // Save session if enabled solve reCAPTCHA once in 30 minutes
                        $_SESSION['fv_recaptcha_session'] = time();
                    }
                } elseif ( fv_setting('recaptcha-session', false) ) {
                    // if if enabled solve reCAPTCHA, but no have session
                    self::echoVoteRes(66, false, $add_subscription);  // need  reCAPTCHA
                } else {
                    fv_log('Vote error - recaptcha_response is empty!', $_POST, __FILE__, __LINE__);
                    self::echoVoteRes(99, false, $add_subscription);     // error
                }
            ENDIF;


//            if ( !$CHECK && !$rating && $contest->voting_type == 'rate' ) {
//                self::echoVoteRes(99, false, $add_subscription);     // Contest voting type == rating but no Rating
//            }
        }

        $add_subscription = apply_filters('fv_vote_contest_add_subscription', $add_subscription, $contest->voting_security_ext, $user_id);

        //** Verify ReCaptcha Response
        $mathCaptcha_solved = false;
        if ( !$CHECK && $contest->voting_security_ext == "mathCaptcha" ) {

            if ( isset( $_POST['math_captcha'] ) && $_POST['math_captcha'] !== '' ) {
                $GLOBALS['current_screen'] = WP_Screen::get( 'front' );
                Math_Captcha()->cookie_session->init_session();
                $session_id = Math_Captcha()->cookie_session->session_ids['default'];
                $math_captcha_answer = (int)$_POST['math_captcha'];

                if ( $session_id !== '' && get_transient( 'mc_' . $session_id ) !== false ) {
                    if ( strcmp( get_transient( 'mc_' . $session_id ), sha1( AUTH_KEY . $math_captcha_answer . $session_id, false ) ) !== 0 )
                        self::echoVoteRes(11, false, $add_subscription);     // wrong
                } else {
                    // Captcha expired
                    $captcha_html = self::get_math_captcha_html();
                    self::echoVoteRes(12, false, $add_subscription, false, false, 0, array('captcha_html' => $captcha_html));     // expired
                }

                unset($math_captcha_answer);
                unset($session_id);
                $mathCaptcha_solved = true;
            } else {
                // Captcha empty
                self::echoVoteRes(11, false, $add_subscription);     // need fill
            }
        }

        // check dates
        if ( ! $contest->isVotingDatesActive() && $contest->isVotingDatesFutureActive() ) {
            self::echoVoteRes(44, false, $add_subscription); // contest not started yet
        }

        if ( $contest->isFinished() || ! $contest->isVotingDatesActive() ) {
            self::echoVoteRes(4, false, $add_subscription); // contest is finished
        }
        // check dates :: END

        $can_vote = false;

        $ip_data = array(
            'ip' => $ip,
            'uid' => $UID,
            'changed' => current_time( 'mysql', 0 ),
            'vote_id' => $vote_id,
            'contest_id' => $contest_id,
            'post_id' => $post_id,
            'browser' => substr($_SERVER['HTTP_USER_AGENT'], 0, 250),
            'display_size' => isset($_POST['ds'])? substr(sanitize_text_field(base64_decode($_POST['ds'])), 0, 49) : '',
            'mouse_pos' => isset($_POST['ms'])? substr(sanitize_text_field(base64_decode($_POST['ms'])), 0, 19) : '-',
            'referer' => substr($referer, 0, 250),
            'user_id' => $user_id,
        );
        
        if ( $rating ) {
            $ip_data['type'] = ModelVotes::$TYPE_RATE;
            $ip_data['rating'] = $rating;
        }
        
        // Check plugins
        if ( !empty($_POST['pp']) ) {
            $ip_data['b_plugins'] = (int)$_POST['pp'];
        }

        $social_condition = false;

        if (session_id() == '') {
            session_start();
        }

        if ( isset($_SESSION['fv_social']) && is_array($_SESSION['fv_social']) ) {
            $ip_data["email"] = substr($_SESSION['fv_social']["email"], 0, 99);
            $ip_data["name"] = substr($_SESSION['fv_social']["soc_name"], 0, 59);
            $ip_data["soc_profile"] = substr($_SESSION['fv_social']["soc_profile"], 0, 249);
            $ip_data["soc_network"] = substr($_SESSION['fv_social']["soc_network"], 0, 49);
            $ip_data["soc_uid"] = substr($_SESSION['fv_social']["soc_network"].$_SESSION['fv_social']["soc_uid"], 0, 49);
            $social_condition = array(
                "soc_uid" => $ip_data["soc_uid"],
            );
            //FvFunctions::dump($ip_data);
        }

        if ( $contest->voting_security_ext == "social" && !is_array($social_condition) ) {
            self::echoVoteRes(99, false, $add_subscription, 0, 0, 0, ['line'=>__LINE__]); // Error
        }

        if ( $contest->voting_security_ext == "fbShare" && !$CHECK ) {
            if ( isset($_POST['fb_post_id']) ) {
                $ip_data["fb_pid"] = (int)$_POST['fb_post_id'];
            /**
             * Fix for FB mobile APP that does not sends a "fb_post_id"
             * {r115}[fix] / 2.2.410
             */
            } elseif ( isset($_POST['referer']) && ('http://m.facebook.com/' == $_POST['referer'] || 'https://m.facebook.com/' == $_POST['referer']) ) {
                $ip_data["fb_pid"] = 'FB mobile refer';
            } else {
                fv_log('FV :: defaultAfb voting => did\'t find facebook share post id', $_POST);
                self::echoVoteRes(99, false, $add_subscription); // did't find facebook share post id
            }
        }


        $ip_data = apply_filters('fv/vote/ip_data', $ip_data, $contest);

        $NEED_check_ip_query = true;
        $check_ip_query = false;
        $check_ip_query_count = false;

        // ============= CHECK if not empty $UID ::START =============
        if ( empty($UID) || strpos($UID, '500 Internal') !== false ) {
            if ($contest->voting_security == "cookies") {
                // Disable QUERY if not have $UID, else will have many records with empty $UID not related with this user
                $NEED_check_ip_query = false;
            } else {
                $UID = 'empty_UID';
            }
        }
        // ============= CHECK if not empty $UID ::END =============

        // ============= if need QUERY to check user :: START =============
        $NEED_check_ip_query = apply_filters('fv/vote/need_check_ip_query', $NEED_check_ip_query);
        IF ( $NEED_check_ip_query === TRUE ) :

            $check_ip_query = ModelVotes::query()->where( "contest_id", $contest->id );

            if ( $contest->voting_security == 'cookiesAip' ) {
                if ( $UID != 'empty_UID' ) {
                    $voting_security_arr = array("ip"=>$ip, "uid" => $UID);
                } eLse {
                    $voting_security_arr = array("ip"=>$ip);
                }

            } else {
                $voting_security_arr = array("uid" => $UID);
            }

            if ($user_id) {
                $voting_security_arr["user_id"] = $user_id;
            }

            // Complete query according to Contest Security Type
            if ( $contest->voting_security_ext == "social" && is_array($social_condition) ) {
                $check_ip_query->where_any( array_merge($voting_security_arr, $social_condition) );
//            } else if ( $contest->voting_security_ext == "fbShare" && !$CHECK ) {
//                $check_ip_query->where_any( array_merge($voting_security_arr, array("fb_pid" => $ip_data["fb_pid"])) );
            } else {
                $check_ip_query->where_any( $voting_security_arr );
            }

            if ( $contest->voting_max_count_total > 0 ) {
                $total_votes = $check_ip_query->find( true );
                if ( $total_votes >= $contest->voting_max_count_total ) {
                    $RES = 22; // user Used all votes per contest
                    self::$used_votes = $total_votes;
                    self::echoVoteRes(22, false, $add_subscription, 0, 0); // echo response
                }
            }

            // Check votes count fot this photo
            $check_ip_query_count = false;
            $gtm_offset = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
            // Complete query according to Contest Voting Frequency

            switch($contest->voting_frequency) {
                case 'once':
                    // $group->contest->date_start
                    $check_ip_query->where_later( 'changed', strtotime($contest->date_start) );
                    break;
                case 'day':
                    $check_ip_query->where_later( 'changed', strtotime(date('Y-m-d 00:00:00')) + $gtm_offset );
                    // $check_ip_query->where_later( "changed", strtotime(date('Y-m-d 00:00:00')) + $gtm_offset );
                    break;
                // ===========================
                default:
                    $check_ip_query->where_later( 'changed', current_time('timestamp', 0) - intval($contest->voting_frequency)*HOUR_IN_SECONDS );
                    break;
            }

            // If Votes count = 1 per photo - limit by PHOTO ID
            if ( $contest->voting_max_count == 0 ) {
                $check_ip_query->where("vote_id", $vote_id);
            }

            // Apply filter to query
            $check_ip_query = apply_filters('fv/vote/check_ip_query', $check_ip_query,
                $contest, $vote_id, $ip, $UID, $ip_data);        // Apply filter to query

/*
            $check_ip_query_count = apply_filters('fv/vote/check_ip_query_count', $check_ip_query_count, $check_ip_query,
                $contest, $vote_id, $ip, $UID, $ip_data);
*/
            $check_ip = $check_ip_query->find();

            //fv_dump_last_query();
/*
            global $wpdb;
            fv_dump($wpdb->last_query);
            fv_dump($check_ip);
*/
            // Apply filter to query results
            $check_ip = apply_filters('fv_vote_check_ip', $check_ip,
                $contest, $user_id, $ip, $UID, $ip_data);
/*
            if ( is_object($check_ip_query_count) ) {
                $check_ip_count = $check_ip_query_count->find();
                //var_dump($check_ip_count);
            }
*/

        ENDIF;  // ============= :: END =============

        /* Защита */
/*
        $cookies = false;
        $cookie_name = '';

        // If user can vote just ONE time per Contest
        if ( $contest->voting_max_count == 1 && $contest->voting_frequency == 'once' ) {
            $cookie_name = 'vote_post_' . $post_id;
        } else {
            $cookie_name = 'vote_post_' . $post_id . '_' . $vote_id;
        }

        // проверяем куку
        if (  !isset($_COOKIE[$cookie_name])  ) {
            if ( !$CHECK ) :
                // если частота голосования - 1 раз
                if ( $contest->voting_frequency == 'once' ){
                    // ставим куку по дате окончания голосования
                    setcookie($cookie_name, strtotime($contest->date_finish), strtotime($contest->date_finish));
                }
            endif;
        } else {
            // if vote frequency - once in 24 hours and exists cookie, and it is correct ( it value later then current WP site time )
            // uses for prevent problems with voting frequency
            if ( $contest->voting_frequency != 'once' && $_COOKIE[$cookie_name] > current_time('timestamp', 0) ) {
                // cookie exists, and is exists database records?
                if ( (is_array($check_ip) && count($check_ip) > 0) OR !$NEED_check_ip_query ) {
                    $cookies = true;
                }
            }
        }

        // Apply filter to query results
        $cookies = apply_filters(FV::PREFIX . 'vote_check_cookies', $cookies,
            $contest->voting_security, $user_id, $ip, $UID, $ip_data);

*/
        $hours_leave = false;  // set default value

        // if voting frequency - once in 24 hours, math in how many hours user can vote
        if ( $contest->voting_frequency != 'once' && is_array($check_ip) && count($check_ip) > 0 ) {
            if ( $contest->voting_frequency != 'day' ) {

                $secs_leave = (strtotime($check_ip[count($check_ip) - 1]->changed, current_time('timestamp', 0)) + intval($contest->voting_frequency) * HOUR_IN_SECONDS) - current_time('timestamp', 0);
                $hours_leave = intval($secs_leave / HOUR_IN_SECONDS);

            } elseif ( $contest->voting_frequency == 'day' ) {
                // Need count time until next day 00:00:00
                $hours_leave = ceil( (strtotime(date('Y-m-d 00:00:00')) + 24*HOUR_IN_SECONDS - current_time( 'timestamp', 0)) / HOUR_IN_SECONDS );
            }

//
//            // if voting frequency - once in 24 hours, set correct cookie related to last vote date
//            if ( !isset($_COOKIE[$cookie_name]) ){
//                // set cookie as Last vote Timestamp + 24 hours
//                $canVoteIn = strtotime($check_ip[count($check_ip)-1]->changed) + intval($contest->voting_frequency) * HOUR_IN_SECONDS;
//                setcookie($cookie_name, $canVoteIn, $canVoteIn);
//            }



        }


        /*
                    if ( (FV::$DEBUG_MODE & FvDebug::$LVL_ALL) || fv_is_lc()  ) {
                        //var_dump($check_ip_query);
                        var_dump($check_ip);

                        var_dump($cookies);

                        echo "current_time mysql = " . current_time( 'mysql', 0 ) . PHP_EOL;
                        echo "secs_passed = " . $secs_leave . PHP_EOL ;
                        echo "hours_passed = " .  $hours_leave . PHP_EOL;
                        var_dump( date( "d-m-Y H:i:s", time() ) );

                    }
        */

        if ( FV::$DEBUG_MODE & FvDebug::LVL_CODE_VOTE ) {
            // Save Voter data, and later may be log it in `self::echoVoteRes`
            self::$vote_debug_var = $ip_data;
            //self::$vote_debug_var['has_cookie'] = $cookies;
            //self::$vote_debug_var['cookie_name'] = $cookie_name;
            self::$vote_debug_var['check_ip_query'] = $check_ip;
        }

        // If not vote, or vote for other photo
        //  && !$cookies
        if (!$check_ip) {
            // User can vote
            $can_vote = true;
        } else {
            //var_dump($cookies);
            //if ( is_array($check_ip) )
/*
            if ( $cookies ) {
                self::echoVoteRes(3, false, $add_subscription, $hours_leave); // 24 hour not passed
            }
*/

            if ( has_filter('fv/vote/process_custom_frequency') ) {
                $can_vote = apply_filters('fv/vote/process_custom_frequency', $can_vote, $contest, $check_ip, false, $add_subscription, $hours_leave);
            } else {
                $code = self::_get_vote_resp_code($contest, $check_ip, $vote_id);
                if ( $code !== TRUE ) {
                    if ( $code === 2 ) {
                        self::$used_votes = count($check_ip);
                    }
                    self::echoVoteRes($code, false, $add_subscription, $hours_leave, 0); // echo response
                }
                $can_vote = true;
            }

        }
        if ( $can_vote && $CHECK ) {
            $extra_args = array();
            if ( $contest->voting_security_ext == "mathCaptcha" && ! $mathCaptcha_solved ) {
                $extra_args['captcha_html'] = self::get_math_captcha_html();
            }
            self::echoVoteRes("can_vote", false, $add_subscription, false, false, 0, $extra_args); // can_vote
        }

        if ($can_vote) {
            $ip_data = FvFunctions::getSpamScore($ip_data, $contest);

            if (session_id() == '') {
                session_start();
            }
            if ( !empty($_SESSION['user_country']) ) {
                self::$user_country = sanitize_text_field($_SESSION['user_country']);
            }

            if ( empty(self::$user_country) ) {
                // Check IP in prev votes
                foreach ($check_ip as $vote_row) {
                    if ($vote_row->ip == $ip) {
                        self::$user_country = $vote_row->user_country;
                        break;
                    }
                }
                if ( empty(self::$user_country) ) {
                    self::$user_country = fv_get_user_country($ip);
                }
                $ip_data['country'] = substr(self::$user_country, 0, 30);
                $_SESSION['user_country'] = self::$user_country;
            }

            // try insert record
            $insert_res = ModelVotes::query()->insert($ip_data);
            $new_votes = 0;
            if ( $insert_res == 0 ) {
                fv_log('Voting :: can`t add new ip record to DB', $ip_data);
                self::echoVoteRes(99, false, $add_subscription); // error
            }
            // Increase vots count
            $competitor = fv_get_competitor($vote_id);
            if ( $competitor ) {
                do_action( 'fv/vote/before_save', $ip_data, $competitor, $contest );
                $new_votes = ModelCompetitors::query()->increaseVotesCount($vote_id, $contest->voting_type, $rating, $contest->isNeedHideVotes());
            } else {
                fv_log('Voting :: can`t find photo by ID =>', $vote_id);
                self::echoVoteRes(99, false, $add_subscription); // error
            }
        }

        // Voted successful
        self::echoVoteRes(1, false, $add_subscription, false, $new_votes);
    }

    /* --------------------------------------------------------------------------- */

    /**
     * Analyze SQL query result and return can user vote or need END
     *
     * @param FV_Contest    $contest
     * @param array         $check_ip
     * @param array|false   $check_ip_count     REMOVED Since 2.2.800
     * @param int           $vote_id
     *
     * @return int
     */
    public static function _get_vote_resp_code($contest, $check_ip, $vote_id)
    {
        // TODO - MAY BE $check_ip_count count from $check_ip in foreach ?
        $exists_count = count($check_ip);
        $exists_count_for_photo = 0;
        // Count - How many votes fore this photo?
        foreach ($check_ip as $vote_row) {
            if ( $vote_row->vote_id == $vote_id ) {
                $exists_count_for_photo++;
            }
        }

        $RES = TRUE;

        if ( $exists_count >= $contest->voting_max_count ) {
            $RES = 2; // user Used all votes at this time
        }

        if ( $exists_count_for_photo ) {
            $RES = 3; // user was already voted for this photo
        }

        return apply_filters('fv/vote/get_resp_code', $RES, $contest, $check_ip, $exists_count, $exists_count_for_photo);
    }

    /**
     * AJAX :: check, is user already entered data (email+name OR social authorization)
     *
     * @return void
     * @output json_array
     */
    public static function is_subscribed()
    {
        //fv_AJAX_response( false, '', array('result'=>'') ); // user was not subscribed      // TODO - remove # var_dump

        if ( !isset($_POST['contest_id']) ) {
            fv_AJAX_response( false, 'Invalid contest ID!' ); // user was not subscribed
        }

        $contest_id = (int)$_POST['contest_id'];
        //$UID = sanitize_text_field($_GET['uid']);
        $contest = ModelContest::query()->findByPK($contest_id, true);

        if (  !is_object($contest)  ) {
            fv_AJAX_response( false ); // user was not subscribed
        }

        // check dates
        if ( ! $contest->isVotingDatesActive() && $contest->isVotingDatesFutureActive() ) {
            fv_AJAX_response( false, fv_get_transl_msg('msg_contest_not_started'), array('result'=>'warning', 'ct_id'=>$contest->id) );
        }

        if ( $contest->isFinished() || ! $contest->isVotingDatesActive() ) {
            fv_AJAX_response( false, fv_get_transl_msg('msg_konkurs_end'), array('result'=>'warning', 'ct_id'=>$contest->id) );  // Contest ended
        }
        // check dates :: END


        $not_subscribed = true;
        $subscribe_hash = '';

        if (session_id() == '') {
            session_start();
        }

        // проверяем куку на подписку
        if ( $contest->isVoteEmailSubscribeRequired() ) {
            $need_mail_verify = fv_setting('mail-verify', true);
            $subscribe_hash = fv_generate_email_subscribe_hash($contest->id, $need_mail_verify);

            if ( isset($_POST['subscribe_hash']) &&
                    fv_verify_email_subscribe_hash($_POST['subscribe_hash'], $contest->id, $need_mail_verify) ) {
                fv_AJAX_response( true, '', array('result'=>'', 'ct_id'=>$contest->id) ); // user was subscribed
            }

            if ( !empty($_SESSION['email_subscribed_'.$contest_id]) ) {
                if ( $need_mail_verify && empty($_SESSION['email_verified_'.$contest_id]) ) {

                    // find email in database
                    $subscriber = ModelSubscribers::query()->findByPK( (int)$_SESSION['email_subscribed_'.$contest_id] );
                    if ( empty($subscriber) || !$subscriber->verified ) {
                        fv_AJAX_response( false, '', array('result'=>'verify_send', 'ct_id'=>$contest->id) ); // user was not subscribed, but email was send
                    }

                }
            } else {
                fv_AJAX_response( false, '', array('result'=>'', 'ct_id'=>$contest->id) ); // user was subscribed
            }
        } else if ( $contest->voting_security_ext == "social" ) {
            if ( !isset($_SESSION['fv_social']) ) {
                fv_AJAX_response( false, '', array('result'=>'', 'ct_id'=>$contest->id) ); // user was not subscribed
            }
        }

        fv_AJAX_response( true, '', array('result'=>'', 'subscribe_hash'=>$subscribe_hash, 'ct_id'=>$contest->id) ); // user was subscribed
    }

    /**
     * Ajax :: save into database Email Subscribe data
     *
     * @output json_array
     */
    public static function email_subscribe()
    {
        if (session_id() == '') {
            session_start();
        }
        check_ajax_referer('fv_vote', 'some_str');
        //die ( fv_json_encode( array('res'=>'not_subscribed') ) ); // user was not subscribed
        $contest_id = (int)$_POST['contest_id'];
        $contestant_id = (int)$_POST['contestant_id'];

        $need_verify = false;
        $verify_send = false;
        $hash = '';

        if ( empty($contestant_id) || empty($contest_id) ) {
            fv_AJAX_response(false, 'Something wrong!');
        }

        /**
         * Allows to add custom validation for $_POST data
         * @since 2.3.08
         */
        do_action('fv/public/pre_validation_email_subscribe', $contest_id, $contestant_id);

        $name = (isset($_POST['fv-name'])) ? sanitize_text_field($_POST['fv-name']) : '';
        $email = (isset($_POST['fv-email'])) ? sanitize_email($_POST['fv-email']) : '';
        $newsletter = (isset($_POST['fv_newsletter']) && $_POST['fv_newsletter'] != "false") ? 1 : 0;

        if ( empty($name) || empty($email) ) {
            fv_AJAX_response( false, fv_get_transl_msg('form_subscr_msg_errors') );
        }

        if ( !is_email($email) ) {
            fv_AJAX_response( false, fv_get_transl_msg('form_subscr_msg_invalid_email') );
        }

        if ( fv_setting('recaptcha-for-subscribe') ) {
            if ( empty( $_POST['g-recaptcha-response']) ) {
                fv_AJAX_response(false, fv_get_transl_msg('msg_recaptcha_wrong'));
            } else {
                $ip = substr( fv_get_user_ip(), 0, 15 );
                $recaptcha_verify = FvFunctions::recaptcha_verify_response( $_POST['g-recaptcha-response'], $ip, fv_setting('recaptcha-secret-key') );

                if ( $recaptcha_verify === 'error' ) {
                    fv_AJAX_response( false, fv_get_transl_msg('msg_recaptcha_wrong'), ['reset_recaptcha'=>1] );
                } elseif ( $recaptcha_verify == false ) {
                    fv_AJAX_response( false, fv_get_transl_msg('msg_recaptcha_wrong'), ['reset_recaptcha'=>1] );
                }
            }
        }

        /**
         * Allows to add custom validation for $_POST data
         * @since 2.3.08
         */
        do_action('fv/public/pre_email_subscribe', $contest_id, $contestant_id);

        $contest = ModelContest::query()->findByPK($contest_id);
        if ( empty($contest) ) {
            fv_AJAX_response(false, 'Invalid contest!');
        }
        if ( !$contest->isVoteEmailSubscribeRequired() ) {
            fv_AJAX_response(false, 'Something is wrong! Try refresh page!');
        }
        $_SESSION['email_verified_'.$contest_id] = false;

        // find email in database
        $subscriber = ModelSubscribers::query()
                        ->where_all( array('email'=>$email, 'type'=>'subscribe') )
                        ->findRow();

        // IF nothing found - fine
        if ( empty($subscriber) ) {

            $to_insert = array(
                'type'          => 'subscribe',
                'contest_id'    => $contest_id,
                'contestant_id' => $contestant_id,
                'name'          => $name,
                'email'         => $email,
                'user_id'       => get_current_user_id(),
                'newsletter'    => $newsletter,
                'verified'      => 0,
            );

            if ( fv_setting('mail-verify', true) ) {
                $need_verify = true;
                $to_insert['verify_hash'] = wp_generate_password( 12, false );
            }

            $to_insert = apply_filters('fv/public/subscribe/filter-data', $to_insert, $contest);

            // Insert row to database
            $subscr_id = ModelSubscribers::query()->insert( $to_insert );

            $_SESSION['email_subscribed_'.$contest_id] = $subscr_id;

            if ( fv_setting('mail-verify', true) ) {
                if ( !empty($contest->page_id) ) {
                    $contest_page_url = get_permalink($contest->page_id);
                } else {
                    $contest_page_url = home_url('/');
                }

                $verify_link = add_query_arg('fv-action', 'verify_mail', $contest_page_url);
                $verify_link = add_query_arg('hash', $to_insert['verify_hash'], $verify_link);
                
                FV_Notifier::sendCustomNotification(
                    'fv/contest/to-user/verify-email',
                    array('user_name' => $name, 'user_email' => $email, 'verify_link' => $verify_link, 'verify_hash'=>$to_insert['verify_hash'])
                );

                $_SESSION['email_verified_' . $contest_id] = false;
            } else {
                // If not need email verify - return Hash
                $hash = fv_generate_email_subscribe_hash($contest_id, false);
            }
        } else {
            // IF email already in database - compare Email & Name

            if ( $subscriber->name != $name || $subscriber->email != $email ) {
                $_SESSION['email_subscribed_'.$contest_id] = false;
                fv_AJAX_response( false, fv_get_transl_msg('form_subscr_msg_found_wrong') );
            } else {
                if ( !$subscriber->verified && fv_setting('mail-verify', true) ) {
                    $need_verify = true;
                    $verify_send = true;
                    $_SESSION['email_subscribed_'.$contest_id] = $subscriber->id;
                } else {
                    $_SESSION['email_verified_'.$contest_id] = $subscriber->id;
                    $_SESSION['email_subscribed_'.$contest_id] = $subscriber->id;
                    $hash = fv_generate_email_subscribe_hash($contest_id, false);
                }
                fv_log('Email subscribe => email entered again with the same name', $email);
            }
        }

        session_write_close();
        fv_AJAX_response(true, '', array('contest_id'=>$contest_id, 'need_verify'=>$need_verify, 'verify_send'=>$verify_send, 'hash'=>$hash));     // Send Success
    }

    /**
     * Ajax ::change Subscribe Email to Verified
     *
     * @output json_array
     */
    public static function email_subscribe_verify_hash()
    {
        if (session_id() == '') {
            session_start();
        }
        check_ajax_referer('fv_vote', 'some_str');

        $hash = (isset($_POST['fv_hash'])) ? sanitize_text_field($_POST['fv_hash']) : '';

        if ( empty($hash) ) {
            fv_AJAX_response( false, "Invalid verification key!" );
        }

        // find Hash in database
        $subscriber = ModelSubscribers::query()
                        ->where_all( array('verify_hash'=>$hash, 'type'=>'subscribe') )
                        ->findRow();

        if ( $subscriber->verified ) {
            fv_AJAX_response( false, fv_get_transl_msg('form_subscr_verify_already_done') );
        }

        // IF nothing found - fine
        if ( !empty($subscriber) ) {
            ModelSubscribers::query()->updateByPK( array('verified'=>1), $subscriber->id );

            $_SESSION['email_subscribed'] = true;
            $_SESSION['email_verified'] = true;
        } else {
            fv_AJAX_response( false, fv_get_transl_msg('form_subscr_verify_error') );
        }

        $_SESSION['email_verified_'.$subscriber->contest_id] = true;
        session_write_close();
        // If not need email verify - return Hash
        $hash = fv_generate_email_subscribe_hash($subscriber->contest_id, true);
        fv_AJAX_response( true, '', array('contest_id'=>$subscriber->contest_id, 'hash'=>$hash) );     // Send Success
    }


    public static function get_math_captcha_html()
    {
        if ( class_exists('Math_Captcha') ) {
            require_once(ABSPATH . '/wp-admin/includes/screen.php');   // For Math Captcha !!
            require_once(ABSPATH . '/wp-admin/includes/class-wp-screen.php');   // For Math Captcha !!

            $GLOBALS['current_screen'] = WP_Screen::get( 'front' );
            Math_Captcha()->cookie_session->init_session();

            return Math_Captcha()->core->generate_captcha_phrase('default');
        } else {
            return 'Please install Math Captcha plugin.';
        }
    }
    
    /**
     * Send mail to user
     *
     * @param int           $code
     * @param deprecated    $deprecated
     * @param bool          $add_subscribsion
     * @param int|bool      $hours_leave
     * @param int|bool      $new_votes
     *
     * @param int           $used_votes
     * @param array         $extra_args
     *
     * @output json_array
     */
    static function echoVoteRes ($code, $deprecated, $add_subscribsion, $hours_leave = false, $new_votes = false, $used_votes = 0, $extra_args = array()) {
        // IF error & code < 100 (> 100 need as example for Payments)
        if ( is_int($code) && $code > 1 && $code < 100 ) {
            if ( (FV::$DEBUG_MODE & FvDebug::LVL_CODE_VOTE) ) {
                $codes_description = array(
                    2 => 'User used all votes at this time',
                    22 => 'User used all votes per contest',
                    3 => 'User was already voted for this photo ',
                    44 => 'date not started',
                    4 => 'date end',
                    5 => 'not authorized',
                    6 => 'wrong reCAPTCHA',
                    7 => 'own photo voting',
                    8 => 'no permissions (role)',
                    9 => 'photo not published',

                    11 => 'Wrong Match Captcha',
                    12 => 'Expired Match Captcha',

                    66 => 'need reCAPTCHA',
                    98 => 'invalid security token',
                    99 => 'error',
                    101 => 'need payment',
                );

                $curr_code_description = ' - ';
                $curr_code_description .= isset($codes_description[$code]) ? $codes_description[$code] : 'no description';

                fv_log('Unsuccessful Voting attempt :: code ' . $code . ' - ' . $curr_code_description, self::$vote_debug_var);
                global $wpdb;
                fv_log('Unsuccessful Voting attempt :: sql ', $wpdb->last_query);
                fv_log('curr memory usage (mb):' . (memory_get_usage() / 1024 / 1024));
            }
            // Count Fail Votes (already voted), reCaptcha fails and errors not need to be counted
            if ( self::$contestant_id && $code < 6 ) {
                ModelCompetitors::query()->increaseFailVotesCount( self::$contestant_id );
            }
        }

        $response_arr = array_merge($extra_args, array(
            'res' => $code,
            'user_country' => self::$user_country,
            'add_subscribsion' => $add_subscribsion,
            'contestant_id' => self::$contestant_id,
            'ct_id' => self::$contest_id,
        ));
        if ( $hours_leave !== false ) {
            $response_arr['hours_leave'] = $hours_leave;
        }
        if ( $new_votes !== false ) {
            $response_arr['new_votes'] = $new_votes;
        }
        $response_arr['used_votes'] = self::$used_votes;

        if ( $code == 1 ) {
            do_action('fv/vote/success', $response_arr);
        }

        $response_arr = apply_filters('fv/vote/echo_res', $response_arr, $code);

        session_write_close();

        die( fv_json_encode($response_arr) );
    }

}