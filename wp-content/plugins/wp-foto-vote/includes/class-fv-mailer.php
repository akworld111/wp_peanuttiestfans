<?php
/**
 * Mailer Class
 *
 * @version  2.2.500
 * @package  FV/Includes
 * @category Helper Class
 * @author   Maxim K
 */
class FV_Mailer {
    /**
     * Set hooks
     */
    public static function beforeSend( $trigger = '' ) {
        $mailfrom = get_option('fotov-users-notify-from-mail', '');

        if ( is_email($mailfrom) ) {
            add_filter( 'wp_mail_from', array( "FV_Mailer", "_mailFromEmail") );
        }

        if ( get_option('fotov-users-notify-from-name') ) {
            add_filter( 'wp_mail_from_name', array( "FV_Mailer", "_mailFromName") );
        }

        if ( FV::$DEBUG_MODE & FvDebug::LVL_MAIL && $trigger ) {
            fv_log('Email triggered: ' . $trigger);
        }

    }
    /**
     * Remove all hooks
     */
    public static function afterSent() {
        remove_filter('wp_mail_from', array("FV_Mailer", "_mailFromEmail"));
        remove_filter('wp_mail_from_name', array("FV_Mailer", "_mailFromName"));
    }
    
    /**
     * Send mail to user
     *
     * @param string $subject       Email subject
     * @param string $body          Email text
     *
     * @return void
     */
    public static function toAdmin( $subject, $body ) {

        if (get_option('fotov-upload-notify-email', false)) {
            $notify_email = get_option('fotov-upload-notify-email');
        } else {
            $notify_email = get_option('admin_email');
        }
        if ( !is_email($notify_email) ) {
            fv_log('notifyMailToAdmin :: Invalid admin Email!', $notify_email);
            return;
        }

        // Add HTML type
        //add_filter('wp_mail_content_type', create_function('', 'return "text/html";'));

        $mailfrom = get_option('fotov-users-notify-from-mail', '');
        if ( is_email($mailfrom) ) {
            add_filter( 'wp_mail_from', array( "FV_Mailer", "_mailFromEmail") );
        }

        if ( get_option('fotov-users-notify-from-name') ) {
            add_filter( 'wp_mail_from_name', array( "FV_Mailer", "_mailFromName") );
        }

        $mail_res = wp_mail( $notify_email, $subject, $body );

        if ( FV::$DEBUG_MODE & FvDebug::LVL_MAIL ) {
            fv_log('Email to admin :: ' . $notify_email . ' with subject[' . $subject . '] with res:' . $mail_res, $body);
        }

        // Сбросим content-type, чтобы избежать возможного конфликта
        //remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
    }

    /**
     * Send mail to user
     *
     * @param string $mailto        Email to send
     * @param string $subject       Email subject
     * @param string $body          Email text
     * @param object $photo         Photo object
     *
     * @return void
     */
    public static function toUser( $mailto, $subject, $body, $photo = null ) {

        $mailfrom = get_option('fotov-users-notify-from-mail', '');

        if ( is_email($mailfrom) ) {
            add_filter( 'wp_mail_from', array( "FV_Mailer", "_mailFromEmail") );
        }

        if ( get_option('fotov-users-notify-from-name') ) {
            add_filter( 'wp_mail_from_name', array( "FV_Mailer", "_mailFromName") );
        }


        $subject = apply_filters( 'fv_user_mail_subject', $subject );
        $body = stripcslashes( apply_filters( 'fv_user_mail_body', $body, $photo ) );

        $mail_res = wp_mail( $mailto, $subject, $body );

        if ( FV::$DEBUG_MODE & FvDebug::LVL_MAIL ) {
            fv_log('Email to ' . $mailto . ' with subject[' . $subject . '] with res:' . $mail_res, $body);
        }

    }

    public static function _mailFromEmail() {
        return get_option('fotov-users-notify-from-mail');
    }

    public static function _mailFromName() {
        return get_option('fotov-users-notify-from-name');
    }
}