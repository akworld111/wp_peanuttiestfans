<?php
/*
Template Name: Prizes
*/

get_header(); ?>
			
	<div class="content">
	
		<div class="inner-content grid-x grid-margin-x grid-padding-x">
	
		    <main class="main small-12 medium-12 large-12 cell" role="main">
            <h1 class="page-title"><?php the_title(); ?></h1>
            <?php
            if ( have_rows( 'prize' ) ) { ?>
                <div class="prizes-block">
                    <div class="grid-container">
                        <div class="main-service-block grid-x grid-margin-x">
                        <?php
                        while ( have_rows( 'prize' ) ) {
                            the_row();
                            $image       = get_sub_field( 'image' );
                            $title       = get_sub_field( 'title' );
                            ?>
                            <div class="prizes-block__item cell large-6 medium-6 medium-offset-0 small-10 small-offset-1">
                                <?php if ( '' !== $image ) { ?>
                                    <img class="img-wrap" src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_html( $title ); ?>">
                                <?php } ?>
                                <?php if ( '' !== $title ) { ?>
                                        <h2 class="title"><?php echo esc_html( $title ); ?></h2>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        </div>
                    </div>
                </div><!--.prizes-block-->
            <?php } ?>					

			</main> <!-- end #main -->
		    
		</div> <!-- end #inner-content -->
	
	</div> <!-- end #content -->

<?php get_footer(); ?>
