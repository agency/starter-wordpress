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
		get_template_directory_uri() . '/assets/js/vendor/modernizr-2.6.2.min.js',
		false,
		'2.6.2',
		false
	);

    // share
    wp_enqueue_script(
        'share',
        get_template_directory_uri() . '/assets/js/plugins/share.js',
        array('jquery'),
        '1.0.0',
        true
    );

	// site
	wp_enqueue_script(
		'site',
		get_template_directory_uri() . '/assets/js/site.js',
		array('jquery', 'share'),
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
	'primary' => 'Site Navigation'
) );

add_theme_support( 'post-thumbnails' );
add_theme_support( 'html5', array('search-form', 'gallery') );
add_editor_style('assets/css/editor.css');

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
