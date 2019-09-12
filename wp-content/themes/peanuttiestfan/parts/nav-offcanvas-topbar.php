<?php
/**
 * The off-canvas menu uses the Off-Canvas Component
 *
 * For more info: http://jointswp.com/docs/off-canvas-menu/
 */
?>

<div class="grid-container ">
	<div class="top-bar">
		<div class="hide-for-large mobile-menu">
			<button class="menu-icon" type="button" data-toggle="off-canvas"></button>
			<?php 
			if (get_theme_mod('custom_logo') && function_exists('the_custom_logo')) {
				the_custom_logo();
			} else { ?>
			<a href="<?php echo home_url(); ?>" class="logo"><img
						src="<?php echo get_template_directory_uri(); ?>/assets/images/pp-logo.png"
						alt="<?php bloginfo('name'); ?>" ></a>
			<?php } ?>
		</div>
		<div class="top-bar-left show-for-large">
		<?php 
			if (get_theme_mod('custom_logo') && function_exists('the_custom_logo')) {
				the_custom_logo();
			} else { ?>
			<ul class="menu">
				<li>
				
				<a href="<?php echo home_url(); ?>" class="logo"><img
						src="<?php echo get_template_directory_uri(); ?>/assets/images/pp-logo.png"
						alt="<?php bloginfo('name'); ?>" ></a></li>
				
			</ul>
		<?php } ?>
		</div>
		<div class="top-bar-right show-for-large">
			<?php joints_top_nav(); ?>
		</div>
	</div>
</div>