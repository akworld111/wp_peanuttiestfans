<?php
/*
Template Name: Fan
*/

get_header(); ?>
			
	<div class="content">
	
		<div class="fan-inner inner-content grid-x grid-margin-x grid-padding-x">
	
		    <main class="main small-12 medium-12 large-12 cell" role="main">
            <h1 class="page-title"><?php the_title(); ?></h1>
            <div class="fan-block">
                <div class="grid-container">
                    <div class="fan-block-wrap grid-x grid-margin-x">
                        <div class="fan-block__item cell large-6 medium-12 medium-offset-0 small-12">
                            <div class="cascading-img">
                                <img class="img-wrap" src="<?php echo get_template_directory_uri(); ?>/assets/images/fan.png" alt="ARE YOU THE PEANUTTIEST FAN?">
                                <div class="overlay-left"><img class="img-wrap" src="<?php echo get_template_directory_uri(); ?>/assets/images/accent.png" alt=""></div>
                                <div class="overlay-right"><img class="img-wrap" src="<?php echo get_template_directory_uri(); ?>/assets/images/accent.png" alt=""></div>
                            </div>
                        </div>
                        <div class="fan-block__item cell large-6 medium-12 medium-offset-0 small-12">
                            <div class="fan-cta">
                                <div class="fan-cta-title">Upload your photo!</div>
                                    <a href="/upload/" class="btn btn-black">Upload</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!--.fan-block-->					

			</main> <!-- end #main -->
		    
		</div> <!-- end #inner-content -->
	
	</div> <!-- end #content -->

<?php get_footer(); ?>
