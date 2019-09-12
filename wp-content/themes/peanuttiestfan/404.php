<?php
/**
 * The template for displaying 404 (page not found) pages.
 *
 * For more info: https://codex.wordpress.org/Creating_an_Error_404_Page
 */

get_header(); ?>
			
	<div class="content">

		<div class="inner-content grid-x grid-margin-x grid-padding-x">
	
			<main class="main small-12 medium-12 large-12 cell" role="main">
				<div class="grid-container">

					<article class="content-not-found">
					
						<header class="article-header">
							<h1 class="page-title">404 Error</h1>
							<h2 class="page-subtitle">Page Not Found</h2>
						</header> <!-- end article header -->
				
						<section class="entry-content">
							<p>Sorry but we could not find the page you were looking for. You can click below to return home.</p>
						</section> <!-- end article section -->

						<section class="back-to-home">
							<a href="/" class="btn btn-success center-block">Return to Peanut Patch Boiled Peanuts</a>
						</section> <!-- end search section -->
				
					</article> <!-- end article -->
				</div>
	
			</main> <!-- end #main -->

		</div> <!-- end #inner-content -->

	</div> <!-- end #content -->

<?php get_footer(); ?>