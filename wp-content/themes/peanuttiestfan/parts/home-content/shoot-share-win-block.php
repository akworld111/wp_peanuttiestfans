<?php
if ( have_rows( 'shoot_share_win' ) ) { ?>
    <div class="shoot_share_win-block">
		<?php $shoot_img = get_field('shoot_share_win_image'); ?>
		<div class="text-center show-for-medium"><img src="<?php if($shoot_img){ echo $shoot_img['url']; } ?>" alt="" class="<?=$shoot_img['alt']?>"></div>
		<div class="text-center show-for-small-only"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/shoot-share-mobile.png" alt="" class="<?=$shoot_img['alt']?>"></div>
        <div class="grid-container">
			<div class="main-service-block grid-x grid-margin-x">
			<?php
			while ( have_rows( 'shoot_share' ) ) {
				the_row();
				$image       = get_sub_field( 'image' );
				$title       = get_sub_field( 'title' );
				$description = get_sub_field( 'description' );
				$extra = get_sub_field( 'extra' );
				?>
				<div class="shoot_share_win-block__item cell large-4 medium-6 medium-offset-0 small-10 small-offset-1">
					<div class="content">
						<div class="description">
						<?php if ( '' !== $title ) { ?>
								<span class="title"><?php echo esc_html( $title ); ?></span>
						<?php } ?>
						<?php if ( '' !== $description ) { ?>
							<?php echo  $description ; ?>
						<?php } ?>
						<?php if ( '' !== $extra ) { ?>
							<span class="extra"><?php echo  $extra ; ?></span>
						<?php } ?>
						</div>
					</div>
				</div>
			<?php } ?>
			</div>
        </div>
    </div><!--.shoot_share_win-block-->
<?php } ?>
