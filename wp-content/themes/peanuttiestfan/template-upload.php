<?php
/*
Template Name: Upload
*/

get_header(); ?>

<?php 
$bgimg = get_field( 'background_image' );
?>
			
<div class="content">
	
    <div class="inner-content grid-x grid-margin-x grid-padding-x">

        <main class="main small-12 medium-12 large-12 cell" role="main">
            
            <div class="grid-container">
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?> role="article" itemscope itemtype="http://schema.org/WebPage">
                                    
                    <section class="entry-content" itemprop="text">
                        <div class="upload-page">
                            <div class="title">Submit a photo</div>
                            <?php the_content(); ?>
                        </div>
                    </section> <!-- end article section -->                                            
                                    
                </article> <!-- end article -->                                   
                    
                <?php endwhile; endif; ?>		
            </div>					

        </main> <!-- end #main -->
        
    </div> <!-- end #inner-content -->

</div> <!-- end #content -->

<?php get_footer(); ?>
