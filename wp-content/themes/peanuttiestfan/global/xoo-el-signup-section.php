<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$gl_options = get_option('xoo-el-general-options');
$redirect 	= !empty( $gl_options['m-register-url'] ) ? esc_attr( $gl_options['m-register-url'] ) : $_SERVER['REQUEST_URI'];
$terms_url 	= !empty( $gl_options['m-terms-url'] ) ? esc_attr( $gl_options['m-terms-url'] ) : null;
?>

<div class="xoo-el-fields">

	<div class="popup-title">Sign Up Now!</span></div>

	<?php xoo_el_print_notices(); ?>

	<div class="popup-switch">Have an account? <a class="xoo-el-login-tgr">Sign In Here</a></div>
	<form class="xoo-el-action-form">

		<?php xoo_el()->fields->get_fields_html(); ?>

		<input type="hidden" name="_xoo_el_form" value="register">

		<?php do_action('xoo_el_register_add_fields'); ?>

		<?php echo xoo_el_recaptcha_html('register'); ?>

		<button type="submit" class="button btn xoo-el-action-btn xoo-el-register-btn"><?php _e('Sign Up','easy-login-woocommerce'); ?></button>

		<div class="terms-message">By signing up, you agree to the terms and privacy.</div>

		<input type="hidden" name="redirect" value="<?php echo $redirect; ?>">

		

		<?php do_action('xoo_el_register_form_end'); ?>

		<?php do_action('xoo_el_register_form_start'); ?>

	</form>
</div>