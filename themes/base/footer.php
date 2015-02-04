	<!-- Page Footer -->
	<section class="page-footer">
		<div class="container">
			<?php if(is_active_sidebar('site-footer')): ?>
				<?php dynamic_sidebar('site-footer'); ?>
			<?php endif; ?>
		</div>
	</section>

	<!-- Footer Menu -->
	<footer class="site-footer clearfix">
		<div class="container">

			<?php wp_nav_menu( array(
				'theme_location' => 'footer-menu',
				'container' => ''
				// 'items_wrap' => '<ul class="menu-secondary">%3$s</ul>'
			)); ?>

		</div>
	</div>

	<?php wp_footer(); ?>

	<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	// paste in code from Google Analytics

	ga('create', '[google-analytics-code]', 'auto');
	ga('require', 'displayfeatures');
	ga('send', 'pageview');

	</script>

</body>
</html>
