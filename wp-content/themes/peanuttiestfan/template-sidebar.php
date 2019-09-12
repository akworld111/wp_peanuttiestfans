<?php
/*
Template Name: Sidebar
*/

get_header(); ?>
			
	<div class="content">
	
		<div class="inner-content grid-x grid-margin-x grid-padding-x">

			<div class="grid-container">
		
				<main class="main small-12 medium-8 large-8 cell" role="main">
					
				
					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				
						<!-- To see additional archive styles, visit the /parts directory -->
						<?php get_template_part( 'parts/loop', 'archive' ); ?>
						
					<?php endwhile; ?>	

						<?php joints_page_navi(); ?>
						
					<?php else : ?>
												
						<?php get_template_part( 'parts/content', 'missing' ); ?>
							
					<?php endif; ?>
																									
				</main> <!-- end #main -->
				
				<?php get_sidebar(); ?>
				
			</div>

		</div> <!-- end #inner-content -->

	</div> <!-- end #content -->

<?php get_footer(); ?>
