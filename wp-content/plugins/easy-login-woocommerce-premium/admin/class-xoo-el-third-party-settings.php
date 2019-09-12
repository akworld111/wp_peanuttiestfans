<?php

class Xoo_El_Third_Party_Settings extends Xoo_El_Admin_Settings{

	protected static $_instance = null;

	public static function get_instance(){
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	

	public function __construct(){
		add_action( 'xoo_sl_loaded', array( $this, 'social_login_functions' ) );
	}

	public function social_login_functions(){
		remove_action( 'admin_menu', array( xoo_sl_admin_settings(), 'add_menu_page' ) );
		add_action( 'admin_menu', array( $this, 'sl_submenu_page' ) );
		add_action( 'admin_enqueue_scripts', array( xoo_sl_admin_settings(), 'enqueue_scripts' ) );
	}

	public function sl_submenu_page(){
		add_submenu_page(
			'xoo-el',
			'Social Login',
			'Social Login',
			'manage_options',
			'xoo-sl',
			array( xoo_sl_admin_settings(), 'menu_page_callback' )
		);
	}

}

function xoo_el_third_party_settings(){
	return Xoo_El_Third_Party_Settings::get_instance();
}



?>