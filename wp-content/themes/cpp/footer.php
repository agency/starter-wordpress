	<footer class="footer footer-main js-footer-cols">
		<div class="container">
			<?php if( has_nav_menu( 'secondary' ) ) {
					wp_nav_menu( array(	
						'theme_location' 	=> 'secondary',
						'container_class'	=> 'nav-secondary-menu-container',
						'menu_class' 			=> 'nav-secondary-menu menu',
						'depth' 					=> 2
					) );
				}
			?>

			<div class="footer-copy">
				<a class="footer-logo" href="">
					<img alt="Climate Council"
						src="<?php echo get_stylesheet_directory_uri(); ?>/assets/dist/img/logo.cc.png" 
						srcset="<?php echo get_stylesheet_directory_uri(); ?>/assets/dist/img/logo.cc@2x.png 2x, <?php echo get_stylesheet_directory_uri(); ?>/assets/dist/img/logo.cc.png 1x" >
				</a>
				<a class="footer-logo" href="">
					<img alt="Cites Power Partnership"
						src="<?php echo get_stylesheet_directory_uri(); ?>/assets/dist/img/logo.png" 
						srcset="<?php echo get_stylesheet_directory_uri(); ?>/assets/dist/img/logo@2x.png 2x, <?php echo get_stylesheet_directory_uri(); ?>/assets/dist/img/logo.png 1x" >
				</a>

				<div class="footer-text">
					<?php if( $footer_text = get_field('footer_text', 'option') ): ?>
						<?php echo $footer_text; ?>
					<?php endif; ?>
					<p>© <?php echo date('Y'); ?> Climate Council</p>
				</div>
			</div>


		</div>
	</footer>

	<?php wp_footer(); ?>
	<script>
	// (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	// 	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	// 	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	// })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	// paste in code from Google Analytics

	// ga('create', '[google-analytics-code]', 'auto');
	// ga('require', 'displayfeatures');
	// ga('send', 'pageview');

	</script>

</body>
</html>