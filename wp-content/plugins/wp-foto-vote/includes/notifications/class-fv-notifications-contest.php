<?php

class FV_Notification_Integration__Contest extends FV_Notification_Integration__Abstract {

    public static $instance = null;
    /**
     * @return array
     */
    function _get_triggers() {
        $triggers = [];
        
        $triggers['fv/contest/to-admin/finished'] = array(
            'class'     => 'FV_Trigger_To_Admin__Contest_Finished',
            'post_title'=> __( 'Contest has been finished', 'fv' ),
            'template' => $this->_get_notification_template('fv/contest/to-admin/finished'),
            'recipients' => array(
                'type' => 'administrator', 'recipient' => ''
            )
        );

        $triggers['fv/contest/to-user/verify-email'] = array(
            'class'     => 'FV_Trigger_To_User__Verify_Email',
            'post_title'=> __( 'Photo contest - please verify your email', 'fv' ),
            'template' => $this->_get_notification_template('fv/contest/to-user/verify-email'),
            'recipients' => array(
                //'recipient_type_slug' => 'MergeTag',
                'type' => 'email', 'recipient' => '{user_email}'
            )
        );

        return apply_filters('fv/notifications/contest/triggers', $triggers);
    }

    /**
     * @param string    $slug
     * @return string
     */
    function _get_notification_template($slug) {

        $html = '<p>' . __( 'Howdy!', 'fv' ) . '</p>';

        switch ($slug) {
            case 'fv/contest/to-admin/finished':
                $html .= '<p>' . __( 'Contest "{contest_name}" has been finished!', 'fv' ) . '</p>';
                $html .= '<p>' . __( 'Winners pick type: {contest_winners_pick}', 'fv' ) . '</p>';
                $html .= '<p>' . __( 'Winners count: {contest_winners_count}', 'fv' ) . '</p>';
                break;
            case 'fv/contest/to-user/verify-email':
                $html .= '<p>' . __( 'For verify your email ({user_email}) please open it (in browser where you enter this email): {verify_link}', 'fv' ) . '</p>';
                $html .= '<p>' . __( 'Or enter this code into form: {verify_hash}', 'fv' ) . '</p>';
                break;
        }

        return apply_filters('fv/notifications/contest/template', $html, $slug);
    }

    function _add_default_tags( $trigger ) {
        $tags = array(
            'contest_name'              => ['StringTag', 'contest', 'Contest Name', 'First contest'],
            'contest_link'              => ['UrlTag', 'contest', 'Contest Link', site_url()],
            'contest_date_start'        => ['StringTag', 'contest', 'Contest Voting Date Start', '2018-01-01 00:00:01'],
            'contest_date_finish'       => ['StringTag', 'contest', 'Contest Voting Date Finish', '2018-06-01 00:00:01'],
            'contest_upload_date_start' => ['StringTag', 'contest', 'Contest Upload Date Start', '2018-01-01 00:00:01'],
            'contest_upload_date_finish'=> ['StringTag', 'contest', 'Contest Upload Date Finish', '2018-06-01 00:00:01'],
            
            'contest_winners_pick'      => ['StringTag', 'contest', 'Contest Winners Pick Type', 'Auto, Manual, ...'],
            'contest_winners_count'     => ['IntegerTag', 'contest', 'Contest Winners Pick Count', 3],
        );

        $this->_register_tags(
            $trigger,
            apply_filters('fv/notifications/contest/default_tags', $tags, $trigger)
        );
    }

    /**
     * @return FV_Notification_Integration__Contest
     */
    public static function get() {
        if ( self::$instance == null ) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}