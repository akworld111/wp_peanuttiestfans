<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Xoo_El_Frontend{

	public function __construct(){
		add_action('wp_enqueue_scripts',array($this,'enqueue_styles'));
		add_action('wp_enqueue_scripts',array($this,'enqueue_scripts'));
		add_action('wp_footer',array($this,'popup_markup'));
		add_shortcode('xoo_el_action',array($this,'markup_shortcode'));
	}


	//Enqueue stylesheets
	public function enqueue_styles(){

		wp_enqueue_style('xoo-el-style',XOO_EL_URL.'/assets/css/xoo-el-style.css',array(),XOO_EL_VERSION);
		wp_enqueue_style('xoo-el-fonts',XOO_EL_URL.'/assets/css/xoo-el-fonts.css',array(),XOO_EL_VERSION);

		$gl_options = get_option('xoo-el-general-options');
		$av_options = get_option('xoo-el-advanced-options');

		$btn_bg_color 	= esc_attr( $gl_options['m-btn-bgcolor'] );
		$btn_txt_color 	= esc_attr( $gl_options['m-btn-txtcolor'] );
		$popup_width 	= esc_attr( $gl_options['m-popup-width'] );
		$popup_height 	= esc_attr( $gl_options['m-popup-height'] );
		$sidebar_img  	= esc_attr( $gl_options['s-sidebar-img']) ;
		$sidebar_width 	= esc_attr( $gl_options['s-sidebar-width'] );
		$sidebar_pos 	= esc_attr( $gl_options['s-sidebar-pos'] );
		$popup_pos 		= esc_attr( $gl_options['s-popup-pos'] );
		$tab_bg_color 		= esc_attr( $gl_options['s-tab-bgcolor'] );
		$tab_actbg_color 	= esc_attr( $gl_options['s-tab-active-bgcolor'] );
		$tab_txt_color 		= esc_attr( $gl_options['s-tab-txtcolor'] );

		$pop_bg_color 	= esc_attr( $av_options['sy-pop-bgcolor'] );
		$pop_txt_color 	= esc_attr( $av_options['sy-pop-txtcolor'] );
		$overlay_color 	= esc_attr( $av_options['sy-overlay-bgcolor'] );
		$overlay_opac 	= esc_attr( $av_options['sy-overlay-opacity'] );
		$inp_bg_color 	= esc_attr( $av_options['sy-input-bgcolor'] );
		$inp_txt_color	= esc_attr( $av_options['sy-input-txtcolor'] );

		$inputs = array(
			'input[type="text"]', 'input[type="password"]', 'input[type="email"]' 
		);

		$select_input_string = $select_input_placeholder_string = '';

		foreach ($inputs as $input) {
			$select_input_string .= '.xoo-el-group '.$input.',';
		}

		foreach ($inputs as $input) {
			$select_input_placeholder_string .= '.xoo-el-group '.$input.'::placeholder,';
		}

		$select_input_string 			= rtrim( $select_input_string, ',' );
		$select_input_placeholder_string = rtrim( $select_input_placeholder_string, ',' );

		$inline_style = "
			button.xoo-el-action-btn{
				background-color: {$btn_bg_color};
				color: {$btn_txt_color};
			}
			.xoo-el-inmodal{
				max-width: {$popup_width}px;
				max-height: {$popup_height}px;
			}
			.xoo-el-sidebar{
    			background-image: url({$sidebar_img});
    			min-width: {$sidebar_width}%;
    		}
    		.xoo-el-main, .xoo-el-main a , .xoo-el-main label{
    			background-color: {$pop_bg_color};
    			color: {$pop_txt_color};
    		}
    		.xoo-el-opac{
    			background-color: {$overlay_color};
    			opacity: {$overlay_opac};
    		}
    		
    		{$select_input_string}{
    			background-color: {$inp_bg_color};
    			color: {$inp_txt_color};
    		}
    		.xoo-el-input-icon, {$select_input_placeholder_string}{
    			color: {$inp_txt_color};
    		}
    		ul.xoo-el-tabs li.xoo-el-active {
    			background-color: {$tab_actbg_color};
    			color: {$tab_txt_color};
    		}
    		ul.xoo-el-tabs {
    			background-color: {$tab_bg_color};
    		}
		";

		

		if($sidebar_pos === 'right'){
			$inline_style .= "
				.xoo-el-wrap{
					direction: rtl;
				}
				.xoo-el-wrap > div{
					direction: ltr;
				}

			";
		}

		if($popup_pos  === 'middle'){
			$inline_style .= "
				.xoo-el-modal:before {
				    content: '';
				    display: inline-block;
				    height: 100%;
				    vertical-align: middle;
				    margin-right: -0.25em;
				}
			";
		}
		else{
			$inline_style .= "
				.xoo-el-inmodal{
					margin-top: 40px;
				}

			";
		}

		//Hide popup trigger links , if user is logged in
		if( is_user_logged_in() ){
			$inline_style .= "
				.xoo-el-login-tgr, .xoo-el-reg-tgr, .xoo-el-lostpw-tgr{
					display: none!important;
				}
			";
		}

		wp_add_inline_style('xoo-el-style',$inline_style);
	}

	//Enqueue javascript
	public function enqueue_scripts(){

		//Enqueue Form field framework scripts
		xoo_el()->fields->enqueue_scripts();
		
		$av_options = get_option( 'xoo-el-advanced-options' );

		wp_enqueue_script('xoo-el-js',XOO_EL_URL.'/assets/js/xoo-el-js.js',array('jquery'),XOO_EL_VERSION,true); // Main JS

		//Password strength meter
		if( $av_options['m-password-meter-enable'] === "yes" ){
			wp_enqueue_script( 'password-strength-meter' );
		}

		//Scrollbar
		if( apply_filters( 'xoo_el_custom_scrollbar', true ) ){
			wp_enqueue_script( 'smooth-scrollbar', XOO_EL_URL.'/library/smooth-scrollbar/smooth-scrollbar.js'); // Main JS
		}

		//Google recaptcha script
		if( $av_options['r-en-recaptcha'] !== "no" ){
			wp_enqueue_script('xoo-el-google-recaptcha','https://www.google.com/recaptcha/api.js?onload=XooElCaptchaCallback&render=explicit"async defer');
			$recaptcha_key =  $av_options['r-recaptcha-sitekey'];
		}

		wp_localize_script('xoo-el-js','xoo_el_localize',array(
			'adminurl'  			=> admin_url().'admin-ajax.php',
			'set_footer_position' 	=> apply_filters('xoo_el_set_footer_position',true),
			'strings'				=> $this->get_js_strings(),
			'password_meter'		=> $av_options['m-password-meter-enable'],
			'force_register'		=> $av_options['sl-register'],
			'recaptcha_key'			=> isset( $recaptcha_key ) ? $recaptcha_key : ''
		));

	}


	//Get Javascript strings
	public function get_js_strings(){
		return array(
			'errors' => array(
				'login' => array(
					'empty' => __('Please fill both the fields','easy-login-woocommerce'), 
				),

				'register' => array(
					'valid_email' 		=> __('Enter valid email address','easy-login-woocommerce'),
					'min_password' 		=> __('Password must be minimum 6 characters.','easy-login-woocommerce'),
					'match_password' 	=> __('Passwords don\'t match.','easy-login-woocommerce'),
					'min_fname' 		=> __('First name must be minimum 3 characters.','easy-login-woocommerce'),
					'min_lname' 		=> __('Last name must be minimum 3 characters.','easy-login-woocommerce'),
					'check_terms' 		=> __('Please accept the terms & conditions.','easy-login-woocommerce'),
				)
			),

			'others' => array(
				'set_password' => __('Please set your password','easy-login-woocommerce')
			)
		);
	}


	//Add popup to footer
	public function popup_markup(){
		 xoo_get_template( 'xoo-el-popup.php', XOO_EL_PATH.'/templates/' );
	}

	//Shortcode
	public function markup_shortcode($user_atts){

		$atts = shortcode_atts( array(
			'action' 			=> 'login', // For version < 1.3
			'type'				=> 'login',
			'change_to' 		=> 'logout',
		), $user_atts, 'xoo_el_action');

		$class = 'xoo-el-action-sc ';

		if( is_user_logged_in() ){

			if( $atts['change_to'] === 'myaccount' ) {
				$change_to_link = wc_get_page_permalink( 'myaccount' );
				$change_to_text =  __('My account','easy-login-woocommerce');
			}
			else{
				$gl_options  	= get_option('xoo-el-general-options');
				$logout_link 	= !empty( $gl_options['m-logout-url'] ) ? $gl_options['m-logout-url'] : $_SERVER['REQUEST_URI'];
				$change_to_link = wp_logout_url( $logout_link );
				$change_to_text =  __('Logout','easy-login-woocommerce');
			}


			$html =  '<a href="'.$change_to_link.'" class="'.$class.'">'.$change_to_text.'</a>';
		}
		else{
			$action_type = isset( $user_atts['action'] ) ? $user_atts['action'] : $atts['type'];
			switch ( $action_type ) {
				case 'login':
					$class .= 'xoo-el-login-tgr';
					$text  	= __('Login','easy-login-woocommerce');
					break;

				case 'register':
					$class .= 'xoo-el-reg-tgr';
					$text  	= __('Signup','easy-login-woocommerce');
					break;

				case 'lost-password':
					$class .= 'xoo-el-lostpw-tgr';
					$text 	= __('Lost Password','easy-login-woocommerce');
					break;
				
				default:
					$class .= 'xoo-el-login-tgr';
					$text 	= __('Login','easy-login-woocommerce');
					break;
			}

			$html = '<a class="'.$class.'">'.$text.'</a>';
		}
		return $html;
	}
}


new Xoo_El_Frontend();

?>
