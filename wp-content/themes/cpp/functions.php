<?php

// Helper Functions
// -------------------------

require_once('inc/extras.php');


// Template Tags
// -------------------------

require_once('inc/template-tags.php');

// Post Types & Taxonomies
// -------------------------

require_once('inc/types.php');
require_once('inc/taxonomies.php');
require_once('inc/postcode.php');

// Shortcodes
// -------------------------

require_once('inc/shortcodes.php');


// Theme
// -------------------------

function base_scripts() {

	// jQuery
	wp_enqueue_script(
		'jquery'
	);

	// modernizr
	wp_enqueue_script(
		'modernizr',
		get_template_directory_uri() . '/assets/dist/js/vendor/modernizr-2.8.3.min.js',
		false,
		'2.6.2',
		false
	);

  // share
  // wp_enqueue_script(
  //   'share',
  //   get_template_directory_uri() . '/assets/dist/js/plugins/share.js',
  //   array('jquery'),
  //   '1.0.0',
  //   true
  // );
  

	// Start Animation scripts
	// ----------------------------
	wp_enqueue_script(
		'tweenmax',
		get_template_directory_uri() . '/assets/dist/js/vendor/TweenMax.min.js',
		false,
		'1.8.5',
		false
	);

	wp_enqueue_script(
		'scrollmagic',
		get_template_directory_uri() . '/assets/dist/js/vendor/ScrollMagic.min.js',
		false,
		'2.0.5',
		false
	);

	wp_enqueue_script(
		'jquery-scrollmagic',
		get_template_directory_uri() . '/assets/dist/js/vendor/jquery.ScrollMagic.min.js',
		array('jquery'),
		'1.0',
		false
	);

	wp_enqueue_script(
		'animationgsap',
		get_template_directory_uri() . '/assets/dist/js/vendor/animation.gsap.min.js',
		false,
		'1.0',
		false
	);

	// End animation
	// ----------------------------


	// Google Maps
	wp_enqueue_script(
    'maps',
    'https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyABnOpbRhD0jdaQY8TEsO-VaHxPwKUKHC8',
    false,
    '1.0.0',
    true
	);


	// site
	wp_enqueue_script(
		'site',
		get_template_directory_uri() . '/assets/dist/js/site.js',
		array('jquery'),
		'1.0.0',
		true
	);



	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

}

add_action( 'wp_enqueue_scripts', 'base_scripts' );



// Theme Settings
// -------------------------

register_nav_menus( array(
	'primary' 	=> 'Site Navigation',
	'secondary' => 'Footer Navigation'
) );

add_theme_support( 'post-thumbnails' );
add_theme_support( 'html5', array('search-form', 'gallery') );
add_editor_style('assets/css/editor.css');

/* Let WordPress manage the document title.
 * By adding theme support, we declare that this theme does not use a
 * hard-coded <title> tag in the document head, and expect WordPress to
 * provide it for us.
 */ 
 
add_theme_support( 'title-tag' );

// Example Sidebar

// register_sidebar(array(
// 	'name' => __( 'Blog Sidebar' ),
// 	'id' => 'blog-sidebar',
// 	'description' => __( 'Widgets across blog pages.' ),
// 	'before_title' => '<h6>',
// 	'after_title' => '</h6><div class="widget-block">',
// 	'before_widget' => '<aside id="%1$s" class="widget blog-widget %2$s">',
// 	'after_widget' => '</div></aside>'
// ));

if (function_exists('acf_add_options_page')) {
    acf_add_options_page();
}

// Image Sizes
// -------------------------

// add_image_size('small-feature', 270, 180, TRUE);
// add_image_size('med-feature', 470, 215, TRUE);
// add_image_size('cropped-thumbnail', 250, 250, TRUE);
add_image_size('hero', 1600, 1600, TRUE);

function mce_editor_buttons( $buttons ) {
    array_unshift( $buttons, 'styleselect' );
    return $buttons;
}

add_filter( 'mce_buttons_2', 'mce_editor_buttons' );

function mce_before_init( $settings ) {

    $style_formats = array(
        array(
            'title' => 'Button',
            'selector' => 'a',
            'classes' => 'button button-primary'
        )
    );

    $settings['style_formats'] = json_encode($style_formats);

    return $settings;

}

add_filter('tiny_mce_before_init', 'mce_before_init');


// Responsive Embeds
// -------------------------

function base_responsive_embeds($html, $url, $attr, $post_id) {

  return '<div class="embed-container">' . $html . '</div>';

}
add_filter('embed_oembed_html', 'base_responsive_embeds', 99, 4);