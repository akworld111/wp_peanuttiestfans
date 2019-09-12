<?php
/*
Template Name: Upload Complete
*/

get_header(); ?>
		
<div class="content">
	
    <div class="inner-content grid-x grid-margin-x grid-padding-x">

        <main class="main small-12 medium-12 large-12 cell" role="main">
            
            <div class="grid-container">
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?> role="article" itemscope itemtype="http://schema.org/WebPage">
                                    
                    <section class="entry-content" itemprop="text">
                        <div class="upload-complete">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/patch-you-re-in.png" alt="You are in" class="upload-complete-img">
                            <div class="message">Your photo has been received and sent to moderators for approval. Submit another photo <a href="/upload/">here</a> or <a href="/vote-to-win/">vote&nbsp;now</a> for this week's winner.</div>
                        </div>
                    </section> <!-- end article section -->                                            
                                    
                </article> <!-- end article -->                                   
                    
                <?php endwhile; endif; ?>		
            </div>					

        </main> <!-- end #main -->
        
    </div> <!-- end #inner-content -->

</div> <!-- end #content -->

<?php get_footer(); ?>
