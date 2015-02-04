<?php get_header(); ?>

	<header class="page-header clearfix">
		<div class="container">
			<div class="header-content">
				<h2><?php if( is_search() ){ echo 'Results for: ' . get_search_query(); } else { _e('Search'); } ?></h2>
			</div>
		</div>
	</header>

	<?php if (have_posts()) : while(have_posts()) : the_post(); ?>

			<section class="page-content">
				<div class="container">

					<div class="primary-content full-width">
						<h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
						<?php the_excerpt(); ?>
						<hr>
					</div>

				</div>
			</section>


		<?php endwhile; ?>
	<?php else: ?>

		<p>Sorry, there weren't any posts that matched your search!</p>

	<?php endif; ?>

<?php get_footer(); ?>
