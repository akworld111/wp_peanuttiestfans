<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$option_name = 'xoo-el-advanced-options';

$settings = array(

	array(
		'type' 			=> 'section',
		'callback' 		=> 'section',
		'id' 			=> 'main-section',
		'title' 		=> 'Main',
	),

	array(
		'type' 			=> 'setting',
		'callback' 		=> 'checkbox',
		'section' 		=> 'main-section',
		'option_name' 	=> $option_name,
		'id' 			=> 'm-password-meter-enable',
		'title' 		=> 'Password Strength Meter',
		'default' 		=> 'yes'
	),

	array(
		'type' 			=> 'section',
		'callback' 		=> 'section',
		'id' 			=> 'social-login-section',
		'title' 		=> 'Social Login',
	),

	array(
		'type' 			=> 'setting',
		'callback' 		=> 'checkbox',
		'section' 		=> 'social-login-section',
		'option_name' 	=> $option_name,
		'id' 			=> 'sl-enable',
		'title' 		=> 'Enable',
		'default' 		=> 'yes'
	),

	array(
		'type' 			=> 'setting',
		'callback' 		=> 'checkbox',
		'section' 		=> 'social-login-section',
		'option_name' 	=> $option_name,
		'id' 			=> 'sl-register',
		'title' 		=> 'Force Registration',
		'default' 		=> 'yes',
		'desc'			=> 'If checked, new user needs to fill the password & other fields'
	),
	
	array(
		'type' 			=> 'section',
		'callback' 		=> 'section',
		'id' 			=> 'recaptcha-section',
		'title' 		=> 'Google Recaptcha',
	),

	array(
		'type' 			=> 'setting',
		'callback' 		=> 'multiple_checkboxes',
		'section' 		=> 'recaptcha-section',
		'option_name' 	=> $option_name,
		'id' 			=> 'r-en-recaptcha',
		'title' 		=> 'Enable Google Recaptcha',
		'default'		=> array(),
		'extra'			=> array(
			'options' => array(
				array(
					'title' 	=> 'Login',
					'value' 	=> 'login',
				),
				array(
					'title' 	=> 'Register',
					'value'   	=> 'register',
				),
				array(
					'title' 	=> 'Lost Password',
					'value'		=> 'lostpw',
				)
			)
		)
	),


	array(
		'type' 			=> 'setting',
		'callback' 		=> 'text',
		'section' 		=> 'recaptcha-section',
		'option_name' 	=> $option_name,
		'id' 			=> 'r-recaptcha-secretkey',
		'title' 		=> 'Recaptcha Secret Key',
		'default' 		=> '',
	),


	array(
		'type' 			=> 'setting',
		'callback' 		=> 'text',
		'section' 		=> 'recaptcha-section',
		'option_name' 	=> $option_name,
		'id' 			=> 'r-recaptcha-sitekey',
		'title' 		=> 'Recaptcha Site Key',
		'default' 		=> '',
	),

	array(
		'type' 			=> 'section',
		'callback' 		=> 'section',
		'id' 			=> 'lla-section',
		'title' 		=> 'Limit Login Attempts',
	),


	array(
		'type' 			=> 'setting',
		'callback' 		=> 'checkbox',
		'section' 		=> 'lla-section',
		'option_name' 	=> $option_name,
		'id' 			=> 'lla-enable',
		'title' 		=> 'Enable',
		'default' 		=> 'no'
	),

	array(
		'type' 			=> 'section',
		'callback' 		=> 'section',
		'id' 			=> 'style-section',
		'title' 		=> 'Style',
	),

	array(
		'type' 			=> 'setting',	
		'callback' 		=> 'color',
		'section' 		=> 'style-section',
		'option_name' 	=> $option_name,
		'id'			=> 'sy-pop-bgcolor',
		'title' 		=> 'Popup Background Color',
		'default' 		=> '#fff'
	),

	array(
		'type' 			=> 'setting',	
		'callback' 		=> 'color',
		'section' 		=> 'style-section',
		'option_name' 	=> $option_name,
		'id'			=> 'sy-pop-txtcolor',
		'title' 		=> 'Popup Text Color',
		'default' 		=> '#000'
	),


	array(
		'type' 			=> 'setting',	
		'callback' 		=> 'color',
		'section' 		=> 'style-section',
		'option_name' 	=> $option_name,
		'id'			=> 'sy-overlay-bgcolor',
		'title' 		=> 'Overlay Background Color',
		'default' 		=> '#000'
	),

	array(
		'type' 			=> 'setting',	
		'callback' 		=> 'number',
		'section' 		=> 'style-section',
		'option_name' 	=> $option_name,
		'id'			=> 'sy-overlay-opacity',
		'title' 		=> 'Overlay Opacity',
		'default' 		=> '0.7'
	),

	array(
		'type' 			=> 'setting',	
		'callback' 		=> 'color',
		'section' 		=> 'style-section',
		'option_name' 	=> $option_name,
		'id'			=> 'sy-input-bgcolor',
		'title' 		=> 'Input Fields Background Color',
		'default' 		=> '#fff'
	),

	array(
		'type' 			=> 'setting',	
		'callback' 		=> 'color',
		'section' 		=> 'style-section',
		'option_name' 	=> $option_name,
		'id'			=> 'sy-input-txtcolor',
		'title' 		=> 'Input Text Color',
		'default' 		=> '#777'
	),


);

return $settings;

?>