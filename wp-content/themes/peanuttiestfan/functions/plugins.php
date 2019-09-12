<?php
/**
 * Basic Options page for global client custom fields. 
 * @uses Advanced Custom Fields Pro (WordPress Plugin)
 */
if ( function_exists( 'acf_add_options_page' ) ){
    require_once( get_template_directory() . '/functions/plugins/client-acf-options.php');
}

/**
 * Edit gravity forms 
 * @uses Gravity Forms (WordPress Plugin)
 */
if ( function_exists( 'gravity_form' ) ){
	// Create a file for gravity forms and change here
    // require_once( ANCHORTHEME_DIR . '/assets/functions/plugins/...'); 
}
