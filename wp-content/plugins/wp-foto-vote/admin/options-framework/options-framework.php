<?php
/**
 * Options Framework
 *
 * @package   Options Framework
 * @author    Devin Price <devin@wptheming.com>
 * @license   GPL-2.0+
 * @link      http://wptheming.com
 * @copyright 2010-2014 WP Theming
 *
 * @wordpress-plugin
 * Plugin Name: Options Framework
 * Plugin URI:  http://wptheming.com
 * Description: A framework for building theme options.
 * Version:     1.8.4
 * Author:      Devin Price
 * Author URI:  http://wptheming.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: optionsframework
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function fv_optionsframework_init() {

	//  If user can't edit theme options, exit
	//if ( !current_user_can( 'edit_theme_options' ) )
	//	return;

	// Load translation files
	//load_plugin_textdomain( 'options-framework', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	// Loads the required Options Framework classes.
	//require 'options-init.php';

    include 'includes/class-options-framework.php';

    include 'includes/class-options-framework-admin.php';
    include 'includes/class-options-interface.php';
    include 'includes/class-options-media-uploader.php';
    include 'includes/class-options-color-rgba.php';
    include 'fields/class-field-taxonomy-dropdown.php';
    include 'fields/class-field-sortable.php';

	// Instantiate the options page.
	$options_framework_admin = new Fv_Options_Framework_Admin;
	$options_framework_admin->init();

	// Instantiate the media uploader class
	$options_framework_media_uploader = new Fv_Options_Framework_Media_Uploader;
	$options_framework_media_uploader->init();

    do_action('fv/addons/init');

}


add_action( 'init', 'fv_optionsframework_init', 20 );


/**
 * Helper function to return the theme option value.
 * If no value has been saved, it returns $default.
 * Needed because options are saved as serialized strings.
 *
 * Not in a class to support backwards compatibility in themes.
 */
/*
if ( ! function_exists( 'of_get_option' ) ) :

function of_get_option( $name, $default = false ) {
	$config = get_option( FV::ADDONS_OPT_NAME );

	if ( ! isset( $config['id'] ) ) {
		return $default;
	}

	$options = get_option( $config['id'] );

	if ( isset( $options[$name] ) ) {
		return $options[$name];
	}

	return $default;
}

endif;
*/