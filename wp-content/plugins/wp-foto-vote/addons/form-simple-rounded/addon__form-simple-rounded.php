<?php
/*
	Form Simple Rounded Design
	Version: 0.1
*/

add_action('plugins_loaded', array('FvExtend_FormSimpleRounded', 'init'), 11);

class FvExtend_FormSimpleRounded {

    /**
     * Performs all the necessary actions
     */
    public static function init()
    {
        add_action( 'fv/load_upload_form/simple-rounded', array('FvExtend_FormSimpleRounded', 'run'), 10, 1 );

        if ( is_admin() ) {
            add_action( 'admin_init', array('FvExtend_FormSimpleRounded', 'admin_init'), 10, 1 );
        }
    }

    public static function run($contest) {
        wp_enqueue_style('fv_simple-rounded', FV::$ADDONS_URL . 'form-simple-rounded/assets/fv_form-simple-rounded.css', false, FV::VERSION, 'all');
    }

    public static function register($contest) {
        echo '<option value="simple-rounded" ' . selected('simple-rounded', $contest->upload_theme) . '>Simple rounded</option>';
    }

    /**
     * Performs all the necessary Admin actions
     */
    public static function admin_init() {
        add_action( 'fv/admin/contest_settings/upload_theme', array('FvExtend_FormSimpleRounded', 'register'), 10, 1 );
    }
}
