<?php

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
