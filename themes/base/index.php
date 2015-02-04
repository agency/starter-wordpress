<?php get_header(); ?>

		<header class="page-header clearfix">

			<div class="container">

				<div class="header-content">

					<h1><?php get_display_title(); ?></h1>

				</div>

			</div>

		</header>

	<?php if (have_posts()) : while(have_posts()) : the_post(); ?>

		<div class="page-content">
			<div class="container">

				<div class="primary-content">

					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>


				</div>

			</div>
		</div>

		<?php endwhile; ?>
	<?php else: ?>

		<p>Sorry, there doesn't seem to be any content for this page!</p>

	<?php endif; ?>

	<?php echo str_replace('<a', '<a class="button"', get_previous_posts_link('&laquo; Previous posts')); ?>
	<?php echo str_replace('<a', '<a class="button"', get_next_posts_link('More posts &raquo;')); ?>

<?php get_footer(); ?>
