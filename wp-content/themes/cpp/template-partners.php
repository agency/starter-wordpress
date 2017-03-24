<?php // Template Name: Partners ?>

<?php get_header(); ?>

<div class="hero -partners">
	<div id="map" class="map" data-map-type="locations"></div>
	<div class="container">
		<h1>
			<?php if( $supertitle = get_field('hero_supertitle') ) { ?><span><?php echo $supertitle; ?></span><?php } ?>
			<?php if( $title = get_field('hero_title')){ echo $title; } else { the_title(); } ?>
		</h1>
	</div>
</div>


<?php if( have_rows('council_stats') ): ?>
	<section class="stats">
		<div class="container">
			<?php while( have_rows('council_stats') ): the_row(); ?>
				
				<div class="stat">
					<?php if($icon = get_sub_field('icon')){ ?><i class="stat-icon icon-<?php echo $icon; ?>"></i><?php } ?>
					<span class="stat-number"><?php the_sub_field('stat'); ?></span>
					<p class="stat-text"><?php the_sub_field('text'); ?></p>
				</div>

			<?php endwhile; ?>
		</div>
	</section>
<?php endif; ?>


<?php get_footer(); ?>