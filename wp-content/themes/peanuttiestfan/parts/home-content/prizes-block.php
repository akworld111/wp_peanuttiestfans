<?php
$main = get_field('main_image');
$prize = get_field('prizes_img'); ?>
    <div class="prizes-block">
        <div class="grid-container">
			<?php if ( '' !== $main ) { ?>
					<div class="text-center"><img class="img-wrap" src="<?php echo esc_url( $main['url'] ); ?>" alt="<?php echo esc_html( $main['alt'] ); ?>"></div>
			<?php } ?>
            <?php if ( '' !== $prize ) { ?>
					<div class="text-center"><img class="img-wrap" src="<?php echo esc_url( $prize['url'] ); ?>" alt="<?php echo esc_html( $prize['alt'] ); ?>"></div>
			<?php } ?>
        </div>
    </div><!--.prizes-block-->
