<?php
/*
Plugin Name: Download Plugins and Themes from Dashboard
Plugin URI: https://wpfactory.com/item/download-plugins-and-themes-from-dashboard/
Description: Download installed plugins and themes ZIP files directly from your admin dashboard without using FTP.
Version: 1.5.0
Author: Algoritmika Ltd
Author URI: http://www.algoritmika.com
Text Domain: download-plugins-dashboard
Domain Path: /langs
Copyright: © 2019 Algoritmika Ltd.
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( 'download-plugins-from-dashboard.php' === basename( __FILE__ ) ) {
	// Check if Pro is active, if so then return
	$plugin = 'download-plugins-from-dashboard-pro/download-plugins-from-dashboard-pro.php';
	if (
		in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ) ) ||
		( is_multisite() && array_key_exists( $plugin, get_site_option( 'active_sitewide_plugins', array() ) ) )
	) {
		return;
	}
}

if ( ! defined( 'ALG_DOWNLOAD_PLUGINS_FILE' ) ) {
	/**
	 * Plugin file constant.
	 *
	 * @since 1.4.0
	 */
	define( 'ALG_DOWNLOAD_PLUGINS_FILE', __FILE__ );
}

if ( ! class_exists( 'Alg_Download_Plugins' ) ) :

/**
 * Main Alg_Download_Plugins Class
 *
 * @class   Alg_Download_Plugins
 * @version 1.4.0
 * @since   1.0.0
 */
final class Alg_Download_Plugins {

	/**
	 * Plugin version.
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	public $version = '1.5.0';

	/**
	 * @var   Alg_Download_Plugins The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main Alg_Download_Plugins Instance.
	 *
	 * Ensures only one instance of Alg_Download_Plugins is loaded or can be loaded.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @static
	 * @return  Alg_Download_Plugins - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Alg_Download_Plugins Constructor.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 * @access  public
	 */
	function __construct() {

		// Translation file
		load_plugin_textdomain( 'download-plugins-dashboard', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );

		// Includes
		$this->settings = require_once( 'includes/settings/class-alg-download-plugins-settings.php' );
		$this->core     = require_once( 'includes/class-alg-download-plugins-core.php' );

		// Action links
		if ( is_admin() ) {
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
		}

	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @version 1.4.0
	 * @since   1.3.0
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
		$custom_links   = array();
		$custom_links[] = '<a href="' . admin_url( 'options-general.php?page=download-plugins-dashboard' ) . '">' . __( 'Settings', 'download-plugins-dashboard' ) . '</a>';
		if ( 'download-plugins-from-dashboard.php' === basename( __FILE__ ) ) {
			$custom_links[] = '<a href="https://wpfactory.com/item/download-plugins-and-themes-from-dashboard/">' . __( 'Unlock All', 'download-plugins-dashboard' ) . '</a>';
		}
		return array_merge( $custom_links, $links );
	}

	/**
	 * Get the plugin url.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  string
	 */
	function plugin_url() {
		return untrailingslashit( plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  string
	 */
	function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

}

endif;

if ( ! function_exists( 'alg_download_plugins' ) ) {
	/**
	 * Returns the main instance of Alg_Download_Plugins to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  Alg_Download_Plugins
	 */
	function alg_download_plugins() {
		return Alg_Download_Plugins::instance();
	}
}

alg_download_plugins();
