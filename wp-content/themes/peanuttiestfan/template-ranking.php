<?php
/*
Template Name: Ranking
*/

get_header(); ?>

<?php 
$bgimg = get_field( 'background_image' );
$title = get_field( 'title_image' );
?>
			
	<div class="content ranking-page" style="background-image: url(<?php echo esc_url( $bgimg['url'] ); ?>)">
	
		<div class="inner-content grid-x grid-margin-x grid-padding-x">
	
		    <main class="main small-12 medium-12 large-12 cell" role="main">
            <div class="grid-container">
            <div class="ranking-content">
            <div class="ranking-title text-center">
                <?php if( '' !== $title ) { ?>
                    <img src="<?php echo esc_url( $title['url'] ); ?>" alt="<?php echo the_title();; ?>">
                <?php } else { ?>
                <h1 class="page-title"><?php the_title(); ?></h1>
                <?php } ?>
            </div>
            <?php
            if ( have_rows( 'ranking' ) ) { ?>
                <div class="ranking-block">
                    <div class="grid-container">
                        <div class="main-service-block grid-x grid-margin-x">
                        <?php
                            $i = 1;
                                while ( have_rows( 'ranking' ) ) {
                                    the_row();
                                    $team       = get_sub_field( 'team' );
                                    $points       = get_sub_field( 'points' );
                                    ?>
                                    <div class="ranking-block__item">
                                        <div class="rank">#<?=$i?></div>
                                        <?php if ( '' !== $team ) { ?>
                                                <div class="title-wrap"><h2 class="title"><?php echo esc_html( $team ); ?></h2></div>
                                        <?php } ?>
                                        <div class="point-wrap">
                                            <?php if ( '' !== $points ) { ?>
                                                <div class="meta-point"><?php echo  $points ; ?></div>
                                                <div class="meta-title">POINTS</div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php $i++; } ?>
                                </div>
                            </div>
                        </div><!--.shoot_share_win-block-->
                    <?php } ?>		

            </div>
            </div>		

			</main> <!-- end #main -->
		    
		</div> <!-- end #inner-content -->
	
	</div> <!-- end #content -->

<?php get_footer(); ?>
