<?php // Template Name: Nominate ?>

<?php get_header(); ?>

<section class="hero -nominate <?php if( has_post_thumbnail() ) { echo '-wbg'; } ?>" style="<?php the_background_image_string('hero'); ?>">
	<div class="container">
		<div class="hero-inner">

			<h1>
				<?php if( $supertitle = get_field('hero_supertitle') ) { ?><span><?php echo $supertitle; ?></span><?php } ?>
				<?php if( $title = get_field('hero_title')){ echo $title; } else { the_title(); } ?>
			</h1>

			<footer class="hero-footer">
				<a href="" class="button -small -icon -facebook"><i class="icon-facebook"></i>Share</a>
				<a href="" class="button -small -icon -twitter"><i class="icon-twitter"></i>Tweet</a>
			</footer>
		</div>
	</div>
</section>


<section class="main -overlap -wsidebar">
	<div class="container -large">

		<aside class="aside-nominate -alt">


			<h4>Find Your Community</h4>

			<form action="" class="form-postcode -condensed">
				<input type="number" class="postcode" id="postcode" name="postcode" placeholder="Enter your postcode" autocomplete="off" min="0" value="<?php echo $_GET['postcode']; ?>">
				<fieldset class="fieldset-submit">
					<a href="" id="postcode-lookup" class="button postcode-submit" data-button-text=""><i class="icon-search"></i></a>
				</fieldset>

				<div class="form-results-wrapper">
					<div class="loading-dots-wrapper"></div>
					<div class="form-result -redirect">
						<p class="-p">Your community is already a Power Partner!</p>
						<a class="button -secondary" id="postcode-redirect-url" href="">See Details</a>
					</div>

					<p class="-p form-result -error">Please enter a valid postcode.</p>
					<p class="-p form-result -pending">Enter your details to nominate your community!</p>
				</div>
				<a href="" id="postcode-redirect-url"></a>
			</form>

			<?php echo do_shortcode('[ninja_form id=2]'); ?>

		</aside>

		<article>
			<?php while(have_posts()) : the_post(); ?>
				<?php the_content(); ?>
			<?php endwhile; ?>


			<div class="nominations -full">
				<div class="bg-extend -pos-left"></div>
				<h5>Who's Been Nominated</h5>
				<div class="nomination">
					<p class="-p nomination-name">Commmunity Name</p>
					<p class="-p nomination-timestamp">Signed 4 minutes ago</p>
				</div>
				<div class="nomination">
					<p class="-p nomination-name">Commmunity Name</p>
					<p class="-p nomination-timestamp">Signed 4 minutes ago</p>
				</div>
				<div class="nomination">
					<p class="-p nomination-name">Commmunity Name</p>
					<p class="-p nomination-timestamp">Signed 4 minutes ago</p>
				</div>
				<div class="nomination">
					<p class="-p nomination-name">Commmunity Name</p>
					<p class="-p nomination-timestamp">Signed 4 minutes ago</p>
				</div>
			</div>
		</article>

	</div>
</section>


<?php get_template_part('partials/cta', 'donate'); ?>


<?php get_footer(); ?>