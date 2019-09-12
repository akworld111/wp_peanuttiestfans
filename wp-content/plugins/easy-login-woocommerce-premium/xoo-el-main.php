<?php
/**
* Plugin Name: WooCommerce Login/Signup Popup Premium
* Plugin URI: http://xootix.com/easy-login-for-woocommerce
* Author: XootiX
* Version: 2.4
* Text Domain: easy-login-woocommerce
* Domain Path: /languages
* Author URI: http://xootix.com
* Description: Allow users to login/signup anywhere from the site with the simple pop up.
* Tags: woocommerce login, woocommerce signup, woocommerce mobile login, otp login, login popup 
*/


//Exit if accessed directly
if(!defined('ABSPATH')){
	return;
}

define("XOO_EL_PRO",true); // Plugin path
define("XOO_EL_PATH",plugin_dir_path(__FILE__)); // Plugin path
define("XOO_EL_URL",plugins_url('',__FILE__)); // plugin url
define("XOO_EL_PLUGIN_BASENAME",plugin_basename( __FILE__ ));
define("XOO_EL_VERSION","2.4"); //Plugin version

/**
 * Check if WooCommerce is activated
 *
 * @since    1.0.0
 */
function xoo_el_init(){
	

	do_action('xoo_el_before_plugin_activation');

	if ( ! class_exists( 'Xoo_El' ) ) {
		require XOO_EL_PATH.'/includes/class-xoo-el.php';
	}

	xoo_el();

	
}
add_action('plugins_loaded','xoo_el_init');

function xoo_el(){
	return Xoo_El::get_instance();
}


/**
 * WooCommerce not activated admin notice
 *
 * @since    1.0.0
 */
function xoo_el_install_wc_notice(){
	?>
	<div class="error">
		<p><?php _e( 'WooCommerce Login/Signup Popup is enabled but not effective. It requires WooCommerce in order to work.', 'easy-login-woocommerce' ); ?></p>
	</div>
	<?php
}
