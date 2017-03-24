
<?php if( $cta_nominate = get_field('cta_nominate', 'option')): ?>
	<section class="cta">
		<div class="section-bg" style="<?php the_background_image_string('hero', get_field('cta_nominate_bg', 'option')); ?>);"></div>
		<div class="container">

			<?php if( $icon = get_field('cta_nominate_icon', 'option')): ?>
				<i class="cta-icon icon-<?php echo $icon; ?>"></i>
			<?php endif; ?>

			<h2><?php echo $cta_nominate; ?></h2>

			<?php the_acf_button('cta_nominate', '-primary', 'option'); ?>

		</div>
	</section>
<?php endif; ?>