<?php
global $post;
$images 		= get_field('cta_donate_bg','option');
$image_arr 	= array();

if( $images ):

	foreach( $images as $image ) {
		$image_arr[] = $image['id']; 
	}

	$i = rand(0, count($image_arr)-1);
	$rand = $image_arr[$i];

endif; ?>


<section class="cta -tall">
	<div class="section-bg" style="<?php the_background_image_string('hero', $rand); ?>);"></div>
	<div class="container">
		
		<?php if( $icon = get_field('cta_donate_icon', 'option')): ?>
			<i class="cta-icon icon-<?php echo $icon; ?>"></i>
		<?php endif; ?>

		<h2><?php the_field('cta_donate', 'option')  ?></h2>

		<?php the_acf_button('cta_donate','-secondary', 'option'); ?>

	</div>
</section>