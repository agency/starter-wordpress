<?php /* Template Name: Full Width */ ?>

<?php get_header(); ?>

	<?php if (have_posts()) while(have_posts()) : the_post(); ?>

			<header class="page-header clearfix">

				<div class="container">

					<div class="header-content">

						<h1><?php the_title(); ?></h1>
						<p>A optional subtitle about this page.</p>

					</div>

				</div>

			</header>

			<section class="page-content">

				<div class="container">

					<div class="primary-content full-width">

						<article>
							<?php the_content(); ?>
						</article>

					</div>

				</div>

			</section>

		<?php endwhile; ?>

<?php get_footer(); ?>
