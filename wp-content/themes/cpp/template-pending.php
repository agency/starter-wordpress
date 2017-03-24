<?php // Template Name: Pending ?>
<?php get_header(); ?>

<?php get_template_part('partials/hero','pending'); ?>


<?php if( $stats = have_rows('campaign_stats') ): ?>
	<section class="stats -overlap">
		<div class="container" data-equal-heights=".stat">
			<?php while( have_rows('campaign_stats') ): the_row(); ?>
				
				<div class="stat">
					<?php if($icon = get_sub_field('icon')){ ?><i class="stat-icon icon-<?php echo $icon; ?>"></i><?php } ?>
					<span class="stat-number"><?php the_sub_field('stat'); ?></span>
					<p class="stat-text"><?php the_sub_field('text'); ?></p>
				</div>

			<?php endwhile; ?>
		</div>
	</section>
<?php endif; ?>

<section class="main <?php if( !$stats ) { echo '-overlap'; } ?>">
	<div class="container">
		<article>
			<?php while(have_posts()) : the_post(); ?>
				<?php the_content(); ?>
			<?php endwhile; ?>
		</article>
	</div>
</section>

<?php get_template_part('partials/cta','donate'); ?>

<?php get_footer(); ?>