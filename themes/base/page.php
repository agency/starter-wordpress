<?php get_header(); ?>

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
					<?php if (have_posts()) while(have_posts()) : the_post(); ?>

					<div class="primary-content">

						<article>
							<?php the_content(); ?>
						</article>
						
					</div>

					<div class="secondary-content">
							<?php get_sidebar(); ?>
					</div>

					<?php endwhile; ?>

				</div>
			</section>

<?php get_footer(); ?>
