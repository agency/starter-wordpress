<?php 
	global $post;
	$slug = basename(get_permalink());
	$class = '-'.$slug;
 ?>

<section class="hero <?php echo $class; ?> <?php if( has_post_thumbnail() ) { echo '-wbg'; } ?>" style="<?php the_background_image_string('hero'); ?>">

	<div class="container">
		<div class="hero-inner">
			<h1>
				<?php if( $supertitle = get_field('hero_supertitle') ) { ?><span><?php echo $supertitle; ?></span><?php } ?>
				<?php if( $title = get_field('hero_title')){ echo $title; } else { the_title(); } ?>
			</h1>
			<?php the_field('hero_text'); ?>
		</div>
	</div>
</section>
