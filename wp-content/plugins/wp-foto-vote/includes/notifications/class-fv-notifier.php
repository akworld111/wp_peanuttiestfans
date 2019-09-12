<?php
/**
 * Notifier Class
 * Send's Mails to users
 *
 * @version  2.2.500
 * @package  FV/Includes
 * @category Helper Class
 * @author   Maxim K
 */
class FV_Notifier {

    /**
     * @param $notification_slug
     * @return string
     * 
     * @since 2.3.00
     */
    static function _normalize_slug ( $notification_slug ) {
        return 'fv/notification/' . str_replace('fv/', '', $notification_slug);
    }

    /**
     * @param FV_Competitor $competitor
     * @param string        $notification_slug
     * @param string        $admin_comment
     *
     * @return bool
     */
    public static function sendCompetitorNotificationToUser( $notification_slug, $competitor, $admin_comment = '' ) {

        $notification_slug = self::_normalize_slug($notification_slug);

        do_action("fv/notifier/send_competitor/user/pre", $notification_slug, $competitor);

        if ( !$user_email = self::_getCompetitorMail($competitor) ) {
            return false;
        }

        $mail_send_res = false;

        FV_Mailer::beforeSend();

        $mail_send_res = do_action( $notification_slug, $competitor, $competitor->getContest(true), $admin_comment );

        //fv_dump($mail_send_res);

        FV_Mailer::afterSent();

        do_action("fv/notifier/send_competitor/user", $notification_slug, $competitor, $mail_send_res);
/*
        $mail_body = fv_replace_mail_tags_to_data(
            fv_get_transl_msg('mail_upload_user_body'),
            $competitor->getContest(true),
            $competitor
        );

        $mail_title = fv_get_transl_msg('mail_upload_user_title');

        $mail_send_res = FV_Mailer::toUser($user_email, $mail_title, $mail_body);

        do_action("fv/notifier/send_competitor_uploaded/ready", $mail_send_res, $competitor, $mail_title, $mail_body);
*/
        return $mail_send_res;
    }

    /**
     * @param FV_Competitor $competitor
     * @param string        $notification_slug
     *
     * @return bool
     */
    public static function sendCompetitorNotificationToAdmin( $notification_slug, $competitor ) {
        $notification_slug = self::_normalize_slug($notification_slug);

        do_action("fv/notifier/send_competitor/admin/pre", $notification_slug, $competitor);

        $mail_send_res = false;

        FV_Mailer::beforeSend();

        $mail_send_res = do_action( $notification_slug, $competitor, $competitor->getContest(true) );

        FV_Mailer::afterSent();

        do_action("fv/notifier/send_competitor/admin", $notification_slug, $competitor, $mail_send_res);

        return $mail_send_res;
    }


    /**
     * @param FV_Contest $contest
     * @param string        $notification_slug
     *
     * @return bool
     */
    public static function sendContestNotificationToAdmin( $notification_slug, $contest ) {
        $notification_slug = self::_normalize_slug($notification_slug);

        do_action("fv/notifier/send_contest/admin/pre", $notification_slug, $contest);

        $mail_send_res = false;

        FV_Mailer::beforeSend();

        $mail_send_res = do_action( $notification_slug, $contest );

        FV_Mailer::afterSent();


        do_action("fv/notifier/send_contest/admin", $notification_slug, $contest, $mail_send_res);

        return $mail_send_res;
    }

    /**
     * @since 2.2.711
     * 
     * @param string        $notification_slug
     * @param array         $notification_data
     *
     * @return bool
     */
    public static function sendCustomNotification( $notification_slug, $notification_data ) {
        $notification_slug = self::_normalize_slug($notification_slug);

        $mail_send_res = false;

        FV_Mailer::beforeSend();

        $mail_send_res = do_action( $notification_slug, $notification_data );

        FV_Mailer::afterSent();

        return $mail_send_res;
    }

    public static function _getAdminEmail() {
        if (get_option('fotov-upload-notify-email', false)) {
            return get_option('fotov-upload-notify-email');
        } else {
            return get_option('admin_email');
        }
    }

    public static function _getCompetitorMail( $competitor ) {

        # Verify Email
        if ( !is_email($competitor->user_email) ) {
            if ( !$competitor->user_id ) {
                return '';
            }
            $competitor->user_email = self::getMailByUserId( $competitor->user_id );
            if ( !$competitor->user_email ) {
                return '';
            }
        }

        return $competitor->user_email;
    }

    /**
     * @param $user_ID
     *
     * @return bool|string
     */
    public static function getMailByUserId($user_ID ) {
        $user = get_userdata($user_ID);
        if ( $user ) {
            return $user->user_email;
        }
        return false;
    }

    /**
     * @param $tag
     * @param FV_Competitor $competitor
     *
     * @return array
     * @access private
     * @since 2.3.00
     */
    public static function _get_competitor_notification_tags_value($tag, $competitor) {
        $value = '';

        switch ($tag):
            case 'competitor_id':
                $value = $competitor->id;
                break;
            case 'competitor_name':
                $value = $competitor->name;
                break;
            case 'competitor_description':
                $value = $competitor->description;
                break;
            case 'competitor_full_description':
                $value = $competitor->full_description;
                break;
            case 'competitor_image_id':
                $value = $competitor->image_id;
                break;
            case 'competitor_status_at_text':
                $value = fv_get_status_name( $competitor->status );
                break;
            case 'competitor_user_email':
                $value = self::_getCompetitorMail($competitor);
                break;
            case 'competitor_user_id':
                $value = $competitor->user_id;
                break;
            case 'competitor_user_ip':
                $value = $competitor->user_ip;
                break;
            case 'competitor_link':
                $value = $competitor->getSingleViewLink();
                break;
            case 'competitor_image_src':
                $value = $competitor->getImageUrl();
                break;
            case 'competitor_place':
                $value = $competitor->getPlaceCaption();
                break;
            case 'competitor_votes':
                $value = $competitor->getVotes();
                break;
            case 'competitor_added_date':
                $value = date('d-m-Y H:i', $competitor->added_date);
                break;
            case 'competitor_categories':
                $value = $competitor->getCategories( 'string' );
                break;
            default:
                continue;
        endswitch;

        if ( false !== strpos($tag, 'competitor_meta_') ) {
            $meta_key = str_replace('competitor_meta_', '', $tag);
            $value = $competitor->meta()->get_value( $meta_key );
        }

        // @deprecated
        // fv/notifications/competitor/get_tags_values
        return apply_filters('fv/notifications/competitor/get_tags_value', $value, $tag, $competitor);
    }
    
    /**
     * @param $tag
     * @param FV_Contest $contest
     *
     * @return array
     * @access private
     * @since 2.3.00
     */
    public static function _get_contest_notification_tags_value($tag, $contest) {
        $value = '';

        switch ($tag):
            case 'contest_name':
                $value = $contest->name;
                break;
            case 'contest_link':
                $value = $contest->getPublicUrl() ? $contest->getPublicUrl() : home_url('/');
                break;
            case 'contest_date_start':
                $value = $contest->date_start;
                break;
            case 'contest_date_finish':
                $value = $contest->date_finish;
                break;
            case 'contest_upload_date_start':
                $value = $contest->upload_date_start;
                break;
            case 'contest_upload_date_finish':
                $value = $contest->upload_date_finish;
                break;
            case 'contest_winners_pick':
                $value = $contest->getWinnersPickTitle();
                break;
            case 'contest_winners_count':
                $value = $contest->winners_count;
                break;
            default:
                continue;
            endswitch;

        return apply_filters('fv/notifications/contest/get_tag_value', $value, $tag, $contest);
    }
}