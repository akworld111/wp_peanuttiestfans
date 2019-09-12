<?php
/*
Template Name: Shoot Share
*/

get_header(); ?>
			
	<div class="content shoot-share">
        <div class="shoot_share_win-block">
            <?php $shoot_img = get_field('shoot_share_win_image'); ?>
            <div class="text-center show-for-medium"><img src="<?php if($shoot_img){ echo $shoot_img['url']; } ?>" alt="<?=$shoot_img['alt']?>"></div>
            <div class="text-center show-for-small-only"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/shoot-share-mobile.png" alt="<?=$shoot_img['alt']?>"></div>
            <div class="grid-container">
                <div class="main-service-block grid-x grid-margin-x">
                <?php
                $i = 1;
                while ( have_rows( 'shoot_share' ) ) {
                    the_row();
                    $image       = get_sub_field( 'image' );
                    $title       = get_sub_field( 'title' );
                    $description = get_sub_field( 'description' );
                    $extra       = get_sub_field( 'extra' );
                    ?>
                    <div class="shoot_share_win-block__item cell large-4 medium-12 small-12">
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
                        <?php if($i == 1) { ?>
                            <div class="content shoot-image">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/shoot-share-back-small.png" alt="" class="shoot-image-small">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/shoot-share-back.png" alt="" class="shoot-image-big">
                            </div>
                        <?php } elseif($i == 2) { ?>
                            <div class="cta mt-325">
                                <div class="cta-sub">SHARE YOUR<br>PHOTO NOW!</div>
                                <?php 
                                    if ( is_user_logged_in() ) {
                                ?>
                                    <a href="/upload/" class="btn">Upload Now</a>
                                <?php 
                                    } else {
                                ?>
                                    <a class="btn xoo-el-reg-tgr">Upload Now</a>
                                <?php 
                                    }
                                ?>
                            </div>
                        <?php } elseif($i == 3) { ?>
                            <div class="cta mt-2">
                                <div class="cta-sub">CAST YOUR<br>VOTE NOW!</div>
                                <a href="/vote-to-win/" class="btn">Vote Now</a>
                            </div>
                        <?php } ?>
                    </div>
                <?php $i++; } ?>
                </div>
            </div>
        </div><!--.shoot_share_win-block-->


	
	</div> <!-- end #content -->

<?php get_footer(); ?>
