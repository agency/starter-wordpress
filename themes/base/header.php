<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
	<meta charset="utf-8">

	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

	<link rel="shortcut icon" href="<?php bloginfo('template_directory'); ?>/favicon.png" type="image/png">

	<!-- Typekit -->
	<script src="//use.typekit.net/ang7kbv.js"></script>
	<script>try{Typekit.load();}catch(e){}</script>

	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/assets/css/site.css">

	<!-- Facebook Meta -->
	<meta property="og:image" content="<?php the_preview_image(); ?>">
	<meta property="og:title" content="<?php wp_title( '|', true, 'right' ); ?>">
	<meta property="og:description" content="<?php the_description(); ?>">
	<meta property="og:url" content="<?php the_permalink(); ?>">
	<meta property="og:site_name" content="<?php bloginfo('name'); ?>">
	<meta property="og:type" content="blog"/>

	<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

	<header class="site-header clearfix">
		<div class="container">

			<div class="site-logo">
				<h2><a href="<?php echo site_url() ?>"><?php echo get_bloginfo('name') ?></a></h2>
			</div>

			<div class="menu">

				<!-- Primary Menu -->
				<?php wp_nav_menu( array(
					'theme_location' => 'primary',
					'container' => '',
					'menu_class' => 'menu-primary'
					)
				); ?>

			</div>

		</div>
	</header>
