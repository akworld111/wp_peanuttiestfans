<?php

class FV_Notification_Integration__System extends FV_Notification_Integration__Abstract {

    public static $instance = null;
    /**
     * @return array
     */
    function _get_triggers() {
        $triggers = [];
        
        $triggers['fv/system/to-admin/reminder-to-erase-subscribers'] = array(
            'class'     => 'FV_Trigger_To_Admin__Reminder_To_Erase_Subscribers',
            'post_title'=> __( 'Reminder to erase Subscribers', 'fv' ),
            'template' => $this->_get_notification_template('fv/system/to-admin/reminder-to-erase-subscribers'),
            'recipients' => array(
                'type' => 'administrator', 'recipient' => ''
            )
        );

        return apply_filters('fv/notifications/system/triggers', $triggers);
    }

    /**
     * @param string    $slug
     * @return string
     */
    function _get_notification_template($slug) {

        $html = '<p>' . __( 'Howdy, admin!', 'fv' ) . '</p>';

        switch ($slug) {
            case 'fv/system/to-admin/reminder-to-erase-subscribers':
                $html .= '<p>' . __( 'It\'s a reminder of the need to clear subscribers data from logs to keep users data more secure:', 'fv' ) . '</p>';
                $html .= '<p>' . '{message}' . '</p>';
                $html .= '<p>' . '{file}' . '</p>';
                break;
        }

        return apply_filters('fv/notifications/contest/template', $html, $slug);
    }

    /**
     * @return FV_Notification_Integration__System
     */
    public static function get() {
        if ( self::$instance == null ) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}