<?php get_header(); ?>

<?php // Template Name: Home ?>

<section class="hero -home <?php if( has_post_thumbnail() ) { echo '-wbg'; } ?>" style="<?php the_background_image_string('hero'); ?>">

	<div class="container">
		<div class="hero-inner">
			<h1 class="-xl">
				<?php if( $supertitle = get_field('hero_supertitle') ) { ?><span><?php echo $supertitle; ?></span><?php } ?>
				<?php if( $title = get_field('hero_title')){ echo $title; } else { the_title(); } ?>
			</h1>

			<form action="" class="form-postcode">
				<input type="number" class="postcode" id="postcode" name="postcode" placeholder="Enter your postcode" autocomplete="off">
				<fieldset class="fieldset-submit">
					<a href="" id="postcode-redirect" class="postcode-submit button -primary animate-loading" data-button-text="Find out Now" min="0">Find out Now</a>
				</fieldset>
				<div class="form-errors"></div>
				<a href="" id="postcode-redirect-url"></a>
			</form>
		</div>
	</div>
</section>



<?php if( $home_intro_title = get_field('home_intro_title') ): ?>
	<section id="intro" class="panel">
		<div class="section-bg -bg-tertiary layer-slow"></div>
		<div class="container">
			<article class="layer-slow">
				<i></i>
				<h3><?php echo $home_intro_title; ?></h3>

				<div class="section-content">
					<?php the_field('home_intro_content'); ?>

					<?php if( $home_intro_button = get_field('home_intro_button')):
						if( get_field('home_intro_button_type') === 'page') {
							$home_intro_button_link = get_field('home_intro_button_url');
						}
						else {
							$home_intro_button_link = get_field('home_intro_button_page');
						}
					?>
						<a href="<?php echo $home_intro_button_link; ?>" class="button -primary"><?php echo $home_intro_button; ?></a>
					<?php endif; ?>
				</div>
			</article>
		</div>
	</section>
<?php endif; ?>



<?php if( $home_secondary_title = get_field('home_secondary_title') ): ?>
	<section id="why" class="panel -wbg">
		<div class="section-bg -alt layer-fast" style="<?php the_background_image_string('hero', get_field('home_secondary_bg')); ?>">
			<?php //the_background_image_string('hero', get_field('home_secondary_bg')); ?>
			<!-- <img src="<?php //the_attachment_src('hero', get_field('home_secondary_bg')); ?>" alt=""> -->
		</div>
		<div class="section-icons -lightbulb"></div>
		<div class="container">
			<article class="layer-slow">
				<h3 class="large"><?php echo $home_secondary_title; ?></h3>

				<?php if( $home_secondary_button = get_field('home_secondary_button')): ?>
					<a href="" class="button -outline"><?php echo $home_secondary_button; ?></a>
				<?php endif; ?>

			</article>
		</div>
	</section>
<?php endif; ?>


<?php if($featured = get_field('home_featured')):?>
	<section id="featured" class="featured">
		<div class="container">
			<?php foreach($featured as $post): setup_postdata($post); ?>
				<?php 
					$categories = get_the_category();
					 
					if ( ! empty( $categories ) ) {
					  $cat = esc_html( $categories[0]->name );   
					}
				 ?>

				<article>
					<div class="featured-thumb" style="background-image:url(http://climatecouncil-cpp.dev/wp-content/uploads/2017/03/content.post_.jpg);">
						<?php if($cat): ?>
							<h6 class="featured-cat"><?php echo $cat; ?></h6>
						<?php endif; ?>
					</div>
					<div class="featured-content">
						<div>
							<h4><?php the_title(); ?></h4>
							<?php the_excerpt(); ?>
							<a href="<?php the_permalink(); ?>" class="button -primary -dark">Read More</a>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</section>
<?php endif; ?>


<?php get_template_part('partials/cta','nominate'); ?>


<?php get_footer(); ?>