<?php
/**
 * The template for displaying the footer. 
 *
 * Comtains closing divs for header.php.
 *
 * For more info: https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */			
 ?>

<?php
$copyright = get_field('copyright_text', 'option');
$foot_logo = get_field('foot_logo', 'option');
$foot_extra = get_field('foot_extra_image', 'option');
?>
					
				<footer class="footer" role="contentinfo">

					<div class="grid-container">
					
						<div class="inner-footer grid-x">
							<div class="small-6 medium-4 large-4 cell">
								<img src="<?php echo $foot_logo['url']; ?>" alt="<?php echo $foot_logo['alt']; ?>" >
							</div>
							
							<div class="small-6 medium-4 large-4 cell">
								<img src="<?php echo $foot_extra['url']; ?>"	alt="<?php echo $foot_extra['alt']; ?>" >
							</div>

							<div class="small-12 medium-4 large-4 cell">
								<div class="foot-copyright">
								<nav role="navigation">
									<?php joints_footer_links(); ?>
								</nav>
								<p>&copy; <?php echo date('Y'); ?> <strong><?php echo $copyright; ?>.</strong></p>
								<p>Innovative Brand Building <strong>Coyne & Co.</strong></P>
								</div>
							</div>
						
						</div> <!-- end #inner-footer -->

					</div>
				
				</footer> <!-- end .footer -->
			
			</div>  <!-- end .off-canvas-content -->
					
		</div> <!-- end .off-canvas-wrapper -->
		
		<?php wp_footer(); ?>
		
	</body>
	
</html> <!-- end page -->