<?php
/**
 *
 * Class FV_Notifications_Core
 *
 * Makes necessary tweaks for "Notifications" plugin
 * @since 2.2.513
 */
class FV_Notifications_Core {
    static $triggers_cache = array();
    static $triggers_cache_fetched = false;

    public static function load() {
        add_filter('plugins_loaded', array('FV_Notifications_Core', 'plugins_loaded__action'), 9);
        add_filter('init', array('FV_Notifications_Core', 'install'));
        add_filter('register_post_type_args', array('FV_Notifications_Core', '_register_post_type_args__notification__filter'), 10, 2);
        add_filter('notification/settings/field/default_value', array('FV_Notifications_Core', '_uninstallation__default_value__filter'), 10, 2);
    }

    /**
     * Use cache when check IS notification exists in database
     *
     * @param string    $trigger
     * @param bool      $return_if_exists
     *
     * @return bool|mixed
     *
     * @since 2.2.608
     */
    public static function _is_notification_defined($trigger, $return_if_exists = false) {
        if ( !self::$triggers_cache_fetched ) {
            global $wpdb;
            self::$triggers_cache = array_merge(
                self::$triggers_cache,
                $wpdb->get_results("SELECT postm.`meta_value` as trigger_slug, `ID`, `post_status`, `post_title` FROM `{$wpdb->posts}` post " .
                    "LEFT JOIN `{$wpdb->postmeta}` as postm ON post.ID = postm.post_id WHERE post.`post_type`='notification' AND postm.`meta_key`='_trigger';", OBJECT_K)
            );
            self::$triggers_cache_fetched = true;
        }

        if ( isset( self::$triggers_cache[$trigger] ) ) {
            return $return_if_exists ? self::$triggers_cache[$trigger] : true;
        } else {
            return false;
        }
    }
    
    public static function plugins_loaded__action() {
        //return;

        if ( !defined( 'NOTIFICATION_DIR' ) && !(isset($_GET['action']) && $_GET['action'] == 'activate') ) {

            if ( version_compare( PHP_VERSION, '5.4', '<' ) ) {
                wp_add_notice( __( 'WP Foto Vote :: Notifications plugin requires PHP in version at least 5.4. WordPress itself <a href="https://wordpress.org/about/requirements/" target="_blank">requires at least PHP 5.6</a>. Please upgrade your PHP version or contact your Server administrator.', 'notification' ) );
            } else {
                //define( 'NOTIFICATION_DIR', FV::$VENDOR_ROOT . 'notification/' );
                require_once FV::$VENDOR_ROOT . 'notification/load.php';
            }

        }

    }



    /**
     * Create all default notifications
     *
     * @param bool $force
     */
    public static function install( $force = false ) {

        if ( !function_exists('register_trigger') ) {
            return;
        }
        
        // version_compare('1.5.219', FV_DB_VERSION) === -1 &&
        $notifications_installed_ver = (int) get_option('fv_notifications_installed');

//        fv_dump( get_post_meta(139) );
//        fv_dump( get_post_meta(140) );
//        die;

        if ( $force || $notifications_installed_ver !== FV_NOTIFICATIONS_VERSION ) {
            self::_install_notifications_from_array( FV_Notification_Integration__Competitor::get()->_get_triggers() );
            self::_install_notifications_from_array( FV_Notification_Integration__Contest::get()->_get_triggers() );
            self::_install_notifications_from_array( FV_Notification_Integration__System::get()->_get_triggers() );

            update_option('fv_notifications_installed', FV_NOTIFICATIONS_VERSION, false, 'yes');
            fv_log("### Notifications installed ###");
        }
    }

    /**
     * @param array $notifications_array
     * @since 2.2.608
     */
    public static function _install_notifications_from_array($notifications_array) {
        $new_post_data=array();
        $notification_post = null;
        foreach ($notifications_array as $notification_slug => $notification_one) {
            $notification_post = self::_is_notification_defined($notification_slug, true);

            if ( ! $notification_post ) {
                $new_post_data = array(
                    'post_content'  => '',
                    'post_title'    => $notification_one['post_title'],
                    'post_name'     => self::_slug_to_post_name($notification_slug),
                    'post_status'   =>  isset($notification_one['post_status']) ? $notification_one['post_status'] : 'publish',    // draft
                    'post_type'     => 'notification',
                );

                $new_post_data['onoffswitch'] = $new_post_data['post_status'] === 'publish' ? 1 : 0;

                $postID = wp_insert_post($new_post_data);


                // ### Put to he cache
                self::$triggers_cache[ $notification_slug ] = (object)array(
                    'ID'            => $postID,
                    'post_status'   => 'publish',
                    'post_title'    => $notification_one['post_title'],
                );
                ### END cache
                if ( $postID && !is_wp_error($postID) ) {
                    add_post_meta($postID, '_trigger', $notification_slug);

                    add_post_meta(
                        $postID,
                        '_notification_type_email',
                        [
                            '_nonce' => NULL,
                            'subject' => $notification_one['post_title'],
                            'body' => $notification_one['template'],
                            'recipients' => [
                                $notification_one['recipients']
                            ],
                        ]
                    );

                    add_post_meta( $postID, '_enabled_notification', 'email' );
                } elseif ( is_wp_error($postID) ) {
                    fv_log("Error installing Notifications", $postID->get_error_message());
                }
            } else {
                // If Notification exists, but not updated to version 5
                if ( ! get_post_meta( $notification_post->ID, '_notification_type_email' ) ) {
                    add_post_meta(
                        $notification_post->ID,
                        '_notification_type_email',
                        [
                            '_nonce' => NULL,
                            'subject' => $notification_one['post_title'],
                            'body' => $notification_one['template'],
                            'recipients' => [
                                $notification_one['recipients']
                            ],
                        ]
                    );
                    add_post_meta( $notification_post->ID, '_enabled_notification', 'email' );
                }
            }
        }        
    }

    public static function _slug_to_post_name($slug) {
        return str_replace(array('/', ' ', '#'), '_', $slug);
    }

    /**
     * Make not "public" to remove SEO and other metaboxes
     *
     * @param $args
     * @param $slug
     * @return mixed
     */
    public static function _register_post_type_args__notification__filter($args, $slug) {
        if ($slug == 'notification') {
            $args['public'] = false;
        }
        return $args;
    }

    /**
     * Change default values to "false" for options "delete settings and notifications" on uninstalling plugin
     *
     * @param $default_value
     * @param $field
     * @return string
     */
    public static function _uninstallation__default_value__filter ($default_value, $field) {
        if ( $field->group() == 'uninstallation' ) {
            if ( in_array($field->slug(), array('notifications', 'settings')) ) {
                return 'false';
            }
        }
        return $default_value;
    }
}