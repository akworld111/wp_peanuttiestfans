<?php

class Xoo_El_Social_Login{

	protected static $_instance = null;

	public static function get_instance(){
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct(){

		$av_options = get_option( 'xoo-el-advanced-options' );

		//If social login is enabled inside popup
		if( $av_options[ 'sl-enable' ] === "yes" ){
			add_action( 'xoo_el_login_form_end', array( __CLASS__ , 'get_social_buttons' ), 10 );
		}

		//If force registration is enabled
		if( $av_options[ 'sl-register' ] === "yes" ){
			add_action( 'xoo_sl_before_processing_userdata', array( $this, 'force_registration'), 10, 1 );
		}

		//Set social login status after account has been created with force register
		add_action( 'xoo_el_created_customer', array( $this, 'handle_user_social_login_status' ), 10, 3 );
	}

	public static function get_social_buttons(){

		if( empty( xoo_sl_active_social_login() ) ) return; //check if any social login is active

		$html  = '<span class="xoo-el-loginvia">'.__( 'or Login Using', 'social-login-woocommerce' ).'</span>';
		ob_start();
		xoo_sl_get_social_buttons(); //Get buttons HTML
		$html .= ob_get_clean();
		echo apply_filters( 'xoo_sl_social_buttons_el_popup', $html );
	}


	public function force_registration( $user_data ){

		$email = sanitize_email( $user_data[ 'email' ] );
		if( email_exists( $email ) ) return; // exit if user is already registered

		//Keep social data in session to update later when user account has been created
		$_SESSION['xoo_el_social_data'] = array(
			'email' => $user_data['email'],
			'type' 	=> $user_data['social_type'],
		);

		wp_send_json(
			array(
				'register' => 'yes',
				'userData' => $user_data
			)
		);

	}

	//Save values stored in session to user meta
	public function handle_user_social_login_status( $customer_id, $new_customer_data, $password_generated ){
		if( !isset( $_SESSION['xoo_el_social_data'] ) ) return;
		$social_data = $_SESSION['xoo_el_social_data'];

		//Update only when social account email id is used while creating account.
		if( $social_data['email'] === $new_customer_data['user_email'] ){
			xoo_sl_handler::update_user_social_login_status( $customer_id, $social_data['type'] );
			//Auto verify user
			if( class_exists( 'Xoo_Uv_Core' ) ){
				xoo_uv_core()->update_user_status( $customer_id, 'active' );
			}
		}
		unset( $_SESSION['xoo_el_social_data'] );
	}

}

function xoo_el_social_login(){
	return Xoo_El_Social_Login::get_instance();
}

?>