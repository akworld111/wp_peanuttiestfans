<?php

/**
 * Class Test_Notifications
 *
 * Class for send test Webhook for contest notifications
 * To test Zapier, etc
 *
 * @since 2.3.00
 */
class Test_Notifications {
    function __construct()
    {
        add_action('notification/admin/metabox/save/post', [$this, 'add_test_button_to_metabox']);
        
        add_action('wp_ajax_fv_test_notifications', [$this, 'AJAX_send_test']);
    }

    function _is_supported_trigger( $trigger ) {
        return false !== strpos( $trigger, '/competitor/' ) || false !== strpos( $trigger, '/contest/' );
    }

    function _get_trigger( $post_ID ) {
        return get_post_meta( $post_ID, '_trigger', true );
    }

    /**
     * @param \BracketSpace\Notification\Utils\View $view
     */
    function add_test_button_to_metabox( $view ){

        $post_ID = $view->get_var( 'post_id' );

        if ( !$this->_is_supported_trigger( $this->_get_trigger($post_ID) ) ) {
            return;
        }

        echo '<button class="button button-send-test" data-post-id="' . $post_ID .'" title="Sends random data to webhook">Send test Webhook</button>';

        $this->assets();
    }
    
    function assets() {
        wp_enqueue_script( 'test-notifications', FV::$ADMIN_URL . 'libs/test-notifications/test-notifications.js', ['fv_lib_js'] );

        wp_localize_script( 'test-notifications', 'TNS', [
            'ajax_url'      => admin_url('admin-ajax.php'),
            'nonce'         => wp_create_nonce('test-notifications'),
        ] );
    }
    
    function AJAX_send_test() {

        try {

            $valid_data = fv_params_validate($_POST, [
                'nonce' => 'required',
                'post_id' => 'required|integer',
            ]);

            if (false === wp_verify_nonce($valid_data['nonce'], 'test-notifications')) {
                fv_AJAX_response(false, 'Invalid nonce, please refresh page!');
            }

            $trigger = $this->_get_trigger($valid_data['post_id']);

            if (!$trigger) {
                fv_AJAX_response(false, 'Wrong Trigger!');
            }

            add_filter('notification/notifications', function ($notifications) {
                unset($notifications['email']);

                return $notifications;
            }, 99);

            /** @var \BracketSpace\Notification\Abstracts\Trigger $trigger_class */
            $trigger_class = notification_get_single_trigger($trigger);

            if ( false !== strpos( $trigger, '/competitor/' ) ) {

                $competitor = ModelCompetitors::q()->where('status', ST_PUBLISHED)
                    ->set_sort_by_type( 'random' )
                    ->findRow();

                if (!$competitor) {
                    fv_AJAX_response(false, 'Please add at least one competitor for test!');
                }

                $trigger_class->_action($competitor, $competitor->getContest());

            } elseif ( false !== strpos( $trigger, '/contest/' ) ) {

                $contest = ModelContest::q()->findRow();

                if (!$contest) {
                    fv_AJAX_response(false, 'Please add at least one contest for test!');
                }

                $trigger_class->_action($contest);

            }

            fv_AJAX_response(true);

        } catch(Exception $ex) {
            fv_AJAX_response(false, 'Server error happens (:');
        }
    }
}