<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$gl_options = get_option('xoo-el-general-options');
$redirect 	= !empty( $gl_options['m-login-url'] ) ? esc_attr( $gl_options['m-login-url'] ) : $_SERVER['REQUEST_URI'];
$en_reg   	= $gl_options['m-en-reg'];

$av_options = (array) get_option( 'xoo-el-advanced-options' );

?>

<div class="xoo-el-fields">

<div class="popup-title">Sign Up Now!</span></div>

	<?php xoo_el_print_notices('login'); ?>

	<form class="xoo-el-action-form">

		<?php do_action('xoo_el_login_form_start'); ?>

		<div class="xoo-aff-fields">

			<div class="xoo-aff-group">
				<label for="xoo-el-username" class="xoo-aff-label">Email address</label>
				<div class="xoo-aff-input-group">
					<input type="text" placeholder="<?php _e('Email','easy-login-woocommerce'); ?>" id="xoo-el-username" name="xoo-el-username" required>
				</div>
			</div>

			<div class="xoo-aff-group">
				<label for="xoo-el-password" class="xoo-aff-label">Password</label>
				<div class="xoo-aff-input-group">
					<input type="password" placeholder="<?php _e('Password','easy-login-woocommerce'); ?>" id="xoo-el-password" name="xoo-el-password" required>
				</div>
			</div>

			<?php do_action('xoo_el_login_add_fields'); ?>

		</div>

		<input type="hidden" name="_xoo_el_form" value="login">

		<?php echo xoo_el_recaptcha_html('login'); ?>

		<button type="submit" class="button btn xoo-el-action-btn xoo-el-login-btn" <?php if( $av_options['lla-enable'] && !xoo_el_is_limit_login_ok() ) echo "disabled"; ?>><?php _e('Sign In','easy-login-woocommerce'); ?></button>

		<input type="hidden" name="redirect" value="<?php echo $redirect; ?>">

		<div class="terms-message"><a class="xoo-el-lostpw-tgr"><?php _e('Forgot Password?','easy-login-woocommerce'); ?></a></div>

		<div class="popup-switch"><a class="xoo-el-reg-tgr">Create account</a></div>


		<?php do_action('xoo_el_login_form_end'); ?>

	</form>
</div>