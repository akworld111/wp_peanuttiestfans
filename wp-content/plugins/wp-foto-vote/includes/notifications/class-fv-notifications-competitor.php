<?php

use BracketSpace\Notification\Defaults\MergeTag;

class FV_Notification_Integration__Competitor extends FV_Notification_Integration__Abstract {

    public static $instance = null;
    
    function __construct () {
        parent::__construct();

        add_action( 'notification/metabox/trigger/tags/after', array($this ,'meta_box_tips__action2'), 10, 1 );
        
        add_action( 'notification/email/message/pre', array($this ,'replace_meta_tags__filter3'), 10, 3 );

        //add_action( 'notification/notify/pre/message', array($this ,'notification_notify__filter4'), 10, 4 );
    }


    /**
     * @param $true
     * @param string $trigger
     * @param $tags
     * @param $affected_objects
     * @internal param WP_Post $post
     *
     * $notify = apply_filters( 'notification/notify', true, $trigger, $tags, $affected_objects );
     */
//    function notification_notify__filter4($true, $trigger, $tags, $affected_objects) {
//        if ( strpos($trigger, '/comment/') !== false ) {
//            echo '<p>' . __( 'You can also use <code>{competitor_meta_*}</code> tags in content.', 'fv' ) . '</p>';
//        }
//
//        return $true;
//    }

    /**
     * @param \underDEV\Notification\Notification\Trigger    $trigger
     */
    function meta_box_tips__action2($trigger) {
        if ( strpos($trigger->get_slug(), 'fv/competitor') !== false ) {
            echo '<p>' . __( 'You can also use <code>{competitor_meta_*}</code> tags in content. <br/>Example: <code>{competitor_meta_url}</code> where "url" is a meta key. <br/><strong>Please disable option "Remove unused merge tags from sent values" in Notifications settings</strong> to allow tags!', 'fv' ) . '</p>';
        }
    }

    /**
     * @param string    $message
     * @param BracketSpace\Notification\Defaults\Notification\Email    $notificaiton
     * @param FV_Trigger_To_User__Approved     $trigger
     * @param BracketSpace\Notification\Interfaces\Triggerable     $trigger2
     * @return string
     * @throws Exception
     *
     * @see https://github.com/Kubitomakita/notification-signature/blob/master/notification-signature.php
     */
    function replace_meta_tags__filter3($message, $notificaiton, $trigger) {

        if ( strpos($trigger->get_slug(), 'fv/competitor') !== false ) {

            if ( empty($trigger->competitor) ) {
                return $message;
            }

            if  ( !$trigger->competitor->objectExists() ) {
                return $message;
            }

            $all_meta = $trigger->competitor->meta()->get_custom_all_flat();

            foreach ($all_meta as $meta_key => $meta_val) {
                echo "replace: ", '{competitor_meta_' .$meta_key. '}', PHP_EOL;
                $message = str_replace('{competitor_meta_' .$meta_key. '}', $meta_val, $message);
            }

            return apply_filters('fv/notifications/competitor/replace_meta_tags', $message, $trigger, $notificaiton);
        }
        return $message;
    }

    /**
         * @return array
     */
    function _get_triggers() {

        $triggers['fv/competitor/to-admin/uploaded'] = array(
            'class'     => 'FV_Trigger_To_Admin__Uploaded',
            'post_title'=> __( 'New competitor submitted', 'fv' ),
            'template' => $this->_get_notification_template('fv/competitor/to-admin/uploaded'),
            'recipients' => array(
                'type' => 'administrator', 'recipient' => ''
            )
        );

        $triggers['fv/competitor/to-user/uploaded'] = array(
            'class'     => 'FV_Trigger_To_User__Uploaded',
            'post_title'=> __( 'Your contest photo successful submitted', 'fv' ),
            'template' => $this->_get_notification_template('fv/competitor/to-user/uploaded'),
            'recipients' => array(
                //'recipient_type_slug' => 'MergeTag',
                'type' => 'email', 'recipient' => '{competitor_user_email}'
            )
        );

        $triggers['fv/competitor/to-user/deleted'] =  array(
            'class'     => 'FV_Trigger_To_User__Deleted',
            'post_title'=> __( 'Your contest photo has been deleted', 'fv' ),
            'template' => $this->_get_notification_template('fv/competitor/to-user/deleted'),
            'recipients' => array(
                'type' => 'email', 'recipient' => '{competitor_user_email}'
            )
        );

        $triggers['fv/competitor/to-user/approved'] = array(
            'class'     => 'FV_Trigger_To_User__Approved',
            'post_title'=> __( 'Your contest photo has been approved', 'fv' ),
            'template' => $this->_get_notification_template('fv/competitor/to-user/approved'),
            'recipients' => array(
                'type' => 'email', 'recipient' => '{competitor_user_email}'
            )
        );

        $triggers['fv/competitor/to-user/winner'] = array(
            'class'     => 'FV_Trigger_To_User__Winner',
            'post_title'=> __( 'Your contest photo has win in contest', 'fv' ),
            'post_status'=> 'draft', // Disabled by default
            'template' => $this->_get_notification_template('fv/competitor/to-user/winner'),
            'recipients' => array(
                //'role'                => 'administrator',
                //'recipient_type_slug' => 'administrator',
                ////'email'       => get_option('admin_email'),
                //'administrator'       => get_option('admin_email'),
                'type' => 'email', 'recipient' => '{competitor_user_email}'
            )
        );
        
        return apply_filters('fv/notifications/competitor/triggers', $triggers);
    }

    /**
     * @param string    $slug
     * @return string
     */
    function _get_notification_template($slug) {

        $html = '<p>' . __( 'Howdy!', 'fv' ) . '</p>';

        switch ($slug) {
            case 'fv/competitor/to-admin/uploaded':
                $html .= '<p>' . __( 'New entry was uploaded with name "{competitor_name}" to contest "{contest_name}".', 'fv' ) . '</p>';
                $html .= '<p>' . __( 'User email: {competitor_user_email}', 'fv' ) . '</p>';
                $html .= '<p>' . __( 'User ip: {competitor_user_id}', 'fv' ) . '</p>';
                break;
            case 'fv/competitor/to-user/uploaded':
                $html .= '<p>' . __( 'Your entry "{competitor_name}" was successful submitted to contest "{contest_name}".', 'fv' ) . '</p>';
                $html .= '<p>' . __( 'After admin review it will appear on this page {contest_link} (you will be notified about review result).', 'fv' ) . '</p>';
                $html .= '<p>' . __( 'Thanks for participating!', 'fv' ) . '</p>';
                break;
            case 'fv/competitor/to-user/deleted':
                $html .= '<p>' . __( 'We are very sorry, but your entry "{competitor_name}" was not passed our validation and has been removed from contest.', 'fv' ) . '</p>';
                $html .= '<p>' . __( '{admin_comment}', 'fv' ) . '</p>';
                $html .= '<p>' . __( 'Thanks for participating!', 'fv' ) . '</p>';
                break;
            case 'fv/competitor/to-user/approved':
                $html .= '<p>' . __( 'Congratulations, your entry "{competitor_name}" was passed our validation and published at contest.', 'fv' ) . '</p>';
                $html .= '<p>' . __( '{admin_comment}', 'fv' ) . '</p>';
                $html .= '<p>' . __( 'Link: {competitor_link}', 'fv' ) . '</p>';
                $html .= '<p>' . __( 'Thanks for participating!', 'fv' ) . '</p>';
                break;
            case 'fv/competitor/to-user/winner':
                $html .= '<p>' . __( 'Congratulations, your entry "{competitor_name}" win in contest.', 'fv' ) . '</p>';
                $html .= '<p>' . __( '{competitor_place} winner with {competitor_votes} votes', 'fv' ) . '</p>';
                $html .= '<p>' . __( 'Link: {competitor_link}', 'fv' ) . '</p>';
                $html .= '<p>' . __( 'Thanks for participating!', 'fv' ) . '</p>';
                break;
        }

        return apply_filters('fv/notifications/competitor/template', $html, $slug);
    }

    /**
     * @param BracketSpace\Notification\Defaults\Trigger\ $trigger
     * @since 2.3.00
     */
    function _add_user_default_tags( $trigger ) {
        $tags = array(
            'competitor_id'             => ['IntegerTag', 'competitor', 'Competitor ID', '35'],
            'competitor_name'           => ['StringTag', 'competitor', 'Competitor Name', 'First'],
            'competitor_description'    => ['StringTag', 'competitor', 'Competitor Description', 'string', false],
            'competitor_full_description' => ['StringTag', 'competitor', 'Competitor Full Description', 'string', false],
            'competitor_categories'     => ['StringTag', 'competitor', 'Competitor Categories list', 'Nature, Wild'],

            'competitor_link'           => ['UrlTag', 'competitor', 'Competitor Link', 'https://site.com/single-photo/958/'],

            'competitor_added_date'     => ['StringTag', 'competitor', 'Contest Upload Date Finish', '2018-06-01 00:00:01'],

            'competitor_image_id'       => ['IntegerTag', 'competitor', 'Competitor User ID', '358'],
            'competitor_status_at_text' => ['StringTag', 'competitor', 'Competitor Status', 'On moderation'],

            'competitor_user_email'     => ['EmailTag', 'competitor', 'Competitor User Email', 'email@gmail.com'],
            'competitor_user_id'        => ['IntegerTag', 'competitor', 'Competitor User ID', '99'],

            'contest_name'              => ['StringTag', 'contest', 'Contest Name', 'First contest'],
            'contest_link'              => ['UrlTag', 'contest', 'Contest Link', site_url()],
            'contest_date_start'        => ['StringTag', 'contest', 'Contest Voting Date Start', '2018-01-01 00:00:01'],
            'contest_date_finish'       => ['StringTag', 'contest', 'Contest Voting Date Finish', '2018-06-01 00:00:01'],
            'contest_upload_date_start' => ['StringTag', 'contest', 'Contest Upload Date Start', '2018-01-01 00:00:01'],
            'contest_upload_date_finish'=> ['StringTag', 'contest', 'Contest Upload Date Finish', '2018-06-01 00:00:01'],
        );
        
        $this->_register_tags(
            $trigger,
            apply_filters('fv/notifications/competitor/user_default_tags', $tags, $trigger)
        );
    }

    /**
     * @param BracketSpace\Notification\Defaults\Trigger\ $trigger
     * @since 2.3.00
     */
    function _add_admin_default_tags( $trigger ) {
        
        $tags = array(
            'competitor_id'             => ['IntegerTag', 'competitor', 'Competitor ID', '35'],
            'competitor_name'           => ['StringTag', 'competitor', 'Competitor Name', 'First'],
            'competitor_description'    => ['StringTag', 'competitor', 'Competitor Description', 'string', false],
            'competitor_full_description' => ['StringTag', 'competitor', 'Competitor Full Description', 'string', false],
            'competitor_categories'     => ['StringTag', 'competitor', 'Competitor Categories list', 'Nature, Wild'],

            'competitor_link'           => ['UrlTag', 'competitor', 'Competitor Link', 'https://site.com/single-photo/958/'],

            'competitor_user_email'     => ['EmailTag', 'competitor', 'Competitor User Email', 'email@gmail.com'],
            'competitor_user_id'        => ['IntegerTag', 'competitor', 'Competitor User ID', '99'],

            'contest_name'              => ['StringTag', 'contest', 'Contest Name', 'First contest'],
            'contest_link'              => ['UrlTag', 'contest', 'Contest Link', site_url()],
            'contest_date_start'        => ['StringTag', 'contest', 'Contest Voting Date Start', '2018-01-01 00:00:01'],
            'contest_date_finish'       => ['StringTag', 'contest', 'Contest Voting Date Finish', '2018-06-01 00:00:01'],
            'contest_upload_date_start' => ['StringTag', 'contest', 'Contest Upload Date Start', '2018-01-01 00:00:01'],
            'contest_upload_date_finish'=> ['StringTag', 'contest', 'Contest Upload Date Finish', '2018-06-01 00:00:01'],
        );
        
        $this->_register_tags(
            $trigger,
            apply_filters('fv/notifications/competitor/admin_default_tags', $tags, $trigger) 
        );
        
    }

    /**
     * @return FV_Notification_Integration__Competitor
     */
    public static function get() {
        if ( self::$instance == null ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

}
//
//if ( strpos($trigger, 'fv/competitor') !== false ) {
//    $matches = array();
//
//    preg_match_all('/\[IF \{([^\}]*)\}\](.[^\]]+)(?:\[ELSE\](.+?))?\[ENDIF\]/s', $message, $matches);
//
//    if ( empty($matches) ) {
//        return $message;
//    }
//
//    $math_tag = '';
//    foreach ( $matches[0] as $m_index => $match )
//    {
//        $math_tag =  trim($matches[1][$m_index]);
//
//        if ( !empty($tags[$math_tag]) ) {
//            // IF value is not empty
//            $message = str_replace($match, $matches[2][$m_index], $message);
//        } elseif( empty($tags[$math_tag]) && $matches[3][$m_index] ) {
//            // ELSE
//            $message = str_replace($match, $matches[3][$m_index], $message);
//        } else {
//            // IF NO ELSE condition - REMOVE ALL
//            $message = str_replace($match, '', $message);
//        }
//    }
//
//    return $message;
//}
