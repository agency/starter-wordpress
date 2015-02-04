<?php get_header(); ?>

			<header class="page-header clearfix">
				<div class="container">
					<div class="header-content">
						<h2><?php the_title(); ?></h2>
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
							<a href="#share"
							class="button-twitter icon-twitter share"
							data-share="twitter"
							data-url="<?php the_permalink() ?>"
							data-via="">Tweet</a>

							<a href="#share"
							class="button-facebook icon-facebook share"
							data-share="facebook"
							data-url="<?php the_permalink() ?>"
							data-via="">Share</a>

						</article>
					</div>

					<div class="secondary-content">
							<?php get_sidebar(); ?>
					</div>

					<?php endwhile; ?>

				</div>
			</section>

<?php get_footer(); ?>
