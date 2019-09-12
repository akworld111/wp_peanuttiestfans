<?php

if( !defined( 'XOO_AFF_DIR' ) ){
	define( 'XOO_AFF_DIR', dirname(__FILE__) );
}

if( !defined( 'XOO_AFF_URL' ) ){
	define( 'XOO_AFF_URL', plugins_url( '', __FILE__  ) );
}

if( !defined( 'XOO_AFF_VERSION' ) ){
	define( 'XOO_AFF_VERSION', '1.0' );
}

//Begin
//$slug -> Generate field framework with the slug
function xoo_aff_fire( $slug ){
		
	if( !$slug ){
		die("You're doing it wrong. Provide a slug");
	}

	include XOO_AFF_DIR.'/includes/class-xoo-aff.php';

	return new Xoo_Aff( $slug ); // Instantiate

	do_action( 'xoo_aff_loaded' );
	
}
?>