<?php

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die('Access denied.');
}

require_once 'wp-page-locker.php';
/**
 * Create Lockers classes
 * @called by "admin_init"
 */
function fv_wp_page_locker_init () {
    new FvSettingsLocking();
    new FvFormsLocking();
    new FvAddonsLocking();
}

/**
 * Class FvSettingsLocking
 * Lock "Photo Contest" => "Settings" page edit
 * URL: admin.php?page=fv-addons
 */
class FvSettingsLocking extends WpPageLocking {
    public function __construct() {
        $redirect_url = admin_url( 'admin.php?page=fv' );
        $edit_url = admin_url( 'admin.php?page=fv-settings' );

        parent::__construct( 'fv-settings', $redirect_url, $edit_url );
    }

    public function get_strings() {
        $strings = array(
            'currently_locked'  => __( 'These settings are currently locked. Click on the <strong>"Request Control"</strong> button to let <strong>%s</strong> know you\'d like to take over.', 'fv' ),
            'currently_editing' => '<strong>%s</strong> is currently editing these settings',
            'taken_over'        => '<strong>%s</strong> has taken over and is currently editing these settings.',
            'lock_requested'    => __( '<strong>%s</strong> has requested permission to take over control of these settings.', 'fv' )
        );

        return array_merge( parent::get_strings(), $strings );
    }

    protected function is_edit_page() {
        return $this->is_page( 'fv-settings' );
    }

    /**
     * @return int
     */
    protected function get_object_id() {
        return 0;
    }

}

/**
 * Class FvAddonsLocking
 * Lock "Photo Contest" => "Addons" page edit
 * URL: admin.php?page=fv-addons
 */
class FvAddonsLocking extends WpPageLocking {
    public function __construct() {
        $redirect_url = admin_url( 'admin.php?page=fv' );
        $edit_url = admin_url( 'admin.php?page=fv-addons' );

        parent::__construct( 'fv-addons', $redirect_url, $edit_url );
    }

    public function get_strings() {
        $strings = array(
            'currently_locked'  => __( 'Addons settings are currently locked. Click on the <strong>"Request Control"</strong> button to let <strong>%s</strong> know you\'d like to take over.', 'fv' ),
            'currently_editing' => '<strong>%s</strong> is currently editing Addons settings',
            'taken_over'        => '<strong>%s</strong> has taken over and is currently editing Addons settings.',
            'lock_requested'    => __( '<strong>%s</strong> has requested permission to take over control of Addons settings.', 'fv' )
        );

        return array_merge( parent::get_strings(), $strings );
    }

    protected function is_edit_page() {
        return $this->is_page( 'fv-addons' );
    }

    protected function get_object_id() {
        return 0;
    }

}

/**
 * Class FvFormsLocking
 * Lock "Photo Contest" => "Forms" edit
 * URL: admin.php?page=fv-formbuilder&form=%d
 */
class FvFormsLocking extends WpPageLocking {
    public function __construct() {
        $redirect_url = admin_url( 'admin.php?page=fv-formbuilder' );
        $form_id      = $this->get_object_id();
        $edit_url     = admin_url( sprintf( 'admin.php?page=fv-formbuilder&form=%d', $form_id ) );

        parent::__construct( 'fv-formbuilder', $redirect_url, $edit_url );
    }

    public function get_strings() {
        $strings = array(
            'currently_locked'  => __( 'These form is currently locked. Click on the <strong>"Request Control"</strong> button to let <strong>%s</strong> know you\'d like to take over.', 'fv' ),
            'currently_editing' => '<strong>%s</strong> is currently editing these form',
            'taken_over'        => '<strong>%s</strong> has taken over and is currently editing these form.',
            'lock_requested'    => __( '<strong>%s</strong> has requested permission to take over control of these form.', 'fv' )
        );

        return array_merge( parent::get_strings(), $strings );
    }

    protected function is_edit_page() {
        return $this->is_page( 'fv-formbuilder' ) && isset($_GET['form']);
    }

    protected function get_object_id() {
        return isset($_GET['form']) ? absint($_GET['form']) : 0;
    }

}