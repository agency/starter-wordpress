<?php // Template Name: Donate ?>

<?php get_header(); ?>

<?php get_template_part('partials/hero'); ?>

<section class="main -overlap -wsidebar">
	<div class="container -large">
		<aside class="aside-donate -alt">
			<?php echo do_shortcode('[payments_form appeal id="132"]'); ?>
		</aside>

		<article>
			<?php while(have_posts()) : the_post(); ?>
				<?php the_content(); ?>
			<?php endwhile; ?>
		</article>
	</div>
</section>

<?php get_footer(); ?>