<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
	<meta charset="utf-8">

	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

	<link rel="shortcut icon" href="<?php bloginfo('template_directory'); ?>/favicon.png" type="image/png">

	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/assets/dist/css/site.min.css">

	<!-- Facebook Meta -->
	<!-- <meta property="og:image" content="<?php //the_preview_image(); ?>">
	<meta property="og:title" content="<?php //wp_title( '|', true, 'right' ); ?>">
	<meta property="og:description" content="<?php //the_description(); ?>">
	<meta property="og:url" content="<?php//the_permalink(); ?>">
	<meta property="og:site_name" content="<?php //bloginfo('name'); ?>">
	<meta property="og:type" content="blog"/> -->

	<script src="https://use.typekit.net/nmu6che.js"></script>
	<script>try{Typekit.load({ async: true });}catch(e){}</script>

	<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>


	<nav class="nav-main">
		<div class="container -full">
			<div class="nav-wrap">
				<a class="nav-logo" href="<?php bloginfo('url'); ?>"">
					<?php get_template_part('partials/logo'); ?>
				</a>

				<button id="nav-toggle" class="nav-toggle">
				  <span></span>
				  <span></span>
				  <span></span>
				  <span></span>
				</button>

				<?php if( has_nav_menu( 'primary' ) ) {
						wp_nav_menu( array(	
							'theme_location' 	=> 'primary',
							'container_class'	=> 'nav-menu-container',
							'menu_class' 			=> 'nav-menu menu',
							'depth' 					=> 2
						) );
					}
				?>
			</div>
			
			<a class="nav-cta" href="">Donate</a>
		</div>
	</nav>


