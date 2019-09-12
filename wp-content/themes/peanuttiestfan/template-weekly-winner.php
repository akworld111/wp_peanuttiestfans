<?php
/*
Template Name: Weekly Winner
*/

$args = array(
    'taxonomy' => 'fv-category',
    'orderby' => 'name',
    'order'   => 'ASC'
);

$cats = get_categories($args);

$catarray = array();

foreach($cats as $cat) {
$catarray[] = $cat->term_id;
}
$category = get_term( max($catarray) );

$url = add_query_arg( 'fv-category',$category->slug,get_permalink() ) ;

if(!isset($_GET['fv-category'])) {
wp_redirect(esc_url($url));
}

get_header(); ?>

<?php


?>
			
	<div class="content">
	
		<div class="inner-content grid-x grid-margin-x grid-padding-x ww-content">
	
        <main class="main small-12 large-12 medium-12 cell" role="main">

            <div class="grid-container">
                
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?> role="article" itemscope itemtype="http://schema.org/WebPage">
						
                        <header class="article-header">
                            <h1 class="page-title"><?php the_title(); ?></h1>
                        </header> <!-- end article header -->
                                        
                        <section class="entry-content" itemprop="text">
                            <?php echo do_shortcode('[fv id="2"]'); ?>
                        </section> <!-- end article section -->
                                        
                    </article> <!-- end article -->
                    
                
                <?php endwhile; endif; ?>	

            </div>						
                                    
            </main> <!-- end #main -->
		    
		</div> <!-- end #inner-content -->
	
	</div> <!-- end #content -->

<?php get_footer(); ?>
