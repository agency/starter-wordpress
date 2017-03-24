<?php get_header(); ?>

<?php get_template_part('partials/hero'); ?>

<section class="main -overlap">
	<div class="container">
		<article>
			<?php while(have_posts()) : the_post(); ?>
				<?php the_content(); ?>
			<?php endwhile; ?>
		</article>
	</div>
</section>

<?php get_footer(); ?>