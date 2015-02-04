<?php

// Site Footer

register_sidebar(array(
	'name' => __( 'Site Footer' ),
	'id' => 'site-footer',
	'before_widget' => '<aside id="%1$s" class="footer-widget %2$s">',
	'after_widget' => '</div></aside>'
));

// Page Sidebar

register_sidebar(array(
	'name' => __( 'Page Sidebar' ),
	'id' => 'page-sidebar',
	'before_widget' => '<aside id="%1$s" class="footer-widget %2$s">',
	'after_widget' => '</div></aside>'
));
