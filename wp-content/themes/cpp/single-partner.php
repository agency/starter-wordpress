<?php get_header(); ?>

<section class="hero -single-partner">
	<div id="map" class="map" data-map-type="read-single" data-lat="-33.8688197" data-lng="151.20929550000005" data-offset="true"></div>
	<div class="js-bg-solid -mobile-full"></div>
	<div class="container">
		<div class="hero-inner">
			<h1><?php the_title(); ?></h1>
			<h5>Is a Power Partner</h5>

			<a href="" class="button -small -icon -facebook"><i class="icon-facebook"></i>Share</a>
			<a href="" class="button -small -icon -twitter"><i class="icon-twitter"></i>Tweet</a>
		</div>
	</div>
</section>



<?php if( $stats = have_rows('partner_stats') ): ?>
	<section class="stats -overlap">
		<div class="container" data-equal-heights=".stat">
			<?php while( have_rows('partner_stats') ): the_row(); ?>
				
				<div class="stat">
					<?php if($icon = get_sub_field('icon')){ ?><i class="stat-icon icon-<?php echo $icon; ?>"></i><?php } ?>
					<span class="stat-number"><?php the_sub_field('stat'); ?></span>
					<p class="stat-text"><?php the_sub_field('text'); ?></p>
				</div>

			<?php endwhile; ?>
		</div>
	</section>
<?php endif; ?>




<section class="main" <?php if( !$stats ) { echo '-overlap'; } ?>">
	<div class="container">
		<?php while(have_posts()) : the_post(); ?>
			<?php the_content(); ?>
		<?php endwhile; ?>
	</div>
</section>



<?php if( have_rows('partner_pledge') ): ?>
	<section class="pledges panel">
		<div class="section-bg -bg-base -alt"></div>
		<div class="container">
			<article>
				<div class="section-header">
					<h3><?php the_title(); ?> Partnership Action Pledge</h3>
					<p>Our power partners are commited to switching to non-polluting energy and reducing emissions in their communities.</p>
				</div>

				<div class="pledge-row" data-equal-heights=".pledge">
				
					<?php $i = 0; while( have_rows('partner_pledge') ): the_row(); $i++; ?>
						<div class="pledge">
							<i class="pledge-icon icon-<?php the_sub_field('icon'); ?> -xlarge"></i>
							<p class="pledge-text"><?php the_sub_field('pledge'); ?></p>
						</div>

						<?php if( ($i % 2 == 0) && ($i != count(get_field('partner_pledge')))) { ?>
						</div><div class="pledge-row" data-equal-heights=".pledge">
						<?php } ?>
					<?php endwhile; ?>

				</div>

				<footer class="section-footer">
					<?php the_acf_button('partner_pledge','-primary -dark'); ?>
				</footer>
			</article>
		</div>
	</section>
<?php endif; ?>



<?php get_template_part('partials/cta', 'donate'); ?>



<?php get_footer(); ?>