<?php

if (!defined('ABSPATH')) {
    die('Access denied.');
}

/**
 * Allows create notice that can be dismissed
 *
 * Class FV_Admin_Dismissible_Notice
 * @since 2.3.00
 */
class FV_Admin_Dismissible_Notice
{
    // Declare variables and constants
    protected static $instance;
    protected $notices = [];
    protected $types = ['info', 'success', 'error', 'warning'];
    protected $option_prefix = 'fv-dismissed-notice-';
    protected $printed = false;

    /**
     * Constructor
     */
    protected function __construct()
    {
        if (is_admin()) {
            add_action('admin_notices', array($this, 'print_notices'));
            add_action('fv_admin_notices', array($this, 'print_notices'));
        }
        add_action('wp_ajax_fv_dismiss_notice', [$this, 'AJAX_dismiss_notice']);

    }

    public function AJAX_dismiss_notice(){
        // Process Dismiss
        if ( ! array_diff_key(['dismiss_notice', 'key', 'save_to', 'hash', '_wpnonce2'], array_keys($_GET)) ) {
            // Verify Nonce
            if ( !wp_verify_nonce($_GET['_wpnonce'], 'dismissible-notice') ) {
                wp_send_json_error();
            }

            // Verify that data was not changed
            if ( $_GET['hash'] !== md5($_GET['key'] . NONCE_SALT . $_GET['save_to']) ) {
                wp_send_json_error();
            }

            // Save data
            $key = $this->option_prefix . $_GET['key'];

            if ( 'option' == $_GET['save_to'] ) {
                add_option( $key, current_time('timestamp') );
            } else if ( 'user_meta' == $_GET['save_to'] ) {
                add_user_meta( get_current_user_id(), $key, current_time('timestamp') );
            }
            wp_send_json_success();
        }
    }

    public function get_dismiss_url( $notice ) {
        return add_query_arg(
            [
                'action'        => 'fv_dismiss_notice',
                'dismiss_notice'=> 1,
                'key'           => $notice['key'],
                'save_to'       => $notice['save_to'],
                'hash'          => md5($notice['key'] . NONCE_SALT . $notice['save_to']),
                '_wpnonce'      => wp_create_nonce( 'dismissible-notice' ),
            ],
            add_query_arg( 'ModPagespeed', 'off', admin_url('admin-ajax.php') )
        );
    }

    public function is_dismissed( $notice ) {
        $key = $this->option_prefix . $notice['key'];

        if ( 'option' == $notice['save_to'] ) {
            return get_option( $key, false );
        } else if ( 'user_meta' == $notice['save_to'] ) {
            return get_user_meta( get_current_user_id(), $key, true );
        }
    }

    /**
     * Queues up a message to be displayed to the user
     *
     * @param string $key Unique key
     * @param string $message The text to show the user
     * @param string $type 'info', 'success', 'error', 'warning'
     * @param string $save_state_to 'options', 'user_meta'
     *
     */
    public function enqueue($key, $message, $type = 'info', $save_state_to = 'option')
    {
        if ( empty($type) || ! in_array($type, $this->types) ) {
            trigger_error("{$type} type is not allowed!", E_USER_WARNING);
        }

        if ( !isset($this->notices[$key]) ) {

            $notice = [
                'key' => $key,
                'type' => $type,
                'save_to' => $save_state_to,
                'message' => (string)$message,
            ];

            if ( !$this->is_dismissed($notice) ) {
                $this->notices[$key] = $notice;
            }
        }
    }

    /**
     * Displays updates and errors
     */
    public function print_notices()
    {

        if ( $this->printed ) {
            return;
        }

        // Print only for users that have access to contest
        if ( ! FvFunctions::curr_user_can() ) {
              return;
        }

        foreach ($this->notices as $notice) {
            require('views/admin-dismissible-notice.php');
        }

        $this->printed = true;
    }


    /**
     * Provides access to a single instances of the class using the singleton pattern
     *
     * @return self
     */
    public static function get()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
} // end Admin_Notice_Helper

FV_Admin_Dismissible_Notice::get();

if (!function_exists('fv_dismissible_notice')) {
    /**
     * Queues up a message to be displayed to the user
     *
     * @param string $key Unique key
     * @param string $message The text to show the user
     * @param string $type 'info', 'success', 'error', 'warning'
     * @param string $save_state_to 'option', 'user_meta'
     *
     */
    function fv_dismissible_notice($key, $message, $type = 'info', $save_state_to = 'option')
    {
        FV_Admin_Dismissible_Notice::get()->enqueue($key, $message, $type, $save_state_to);
    }
}
