<?php

// Includes

require_once('inc/extras.php');
require_once('inc/types.php');
require_once('inc/shortcodes.php');
require_once('inc/scripts.php');
require_once('inc/menus.php');
require_once('inc/editor.php');
require_once('inc/sidebars.php');
require_once('inc/embeds.php');
require_once('inc/images.php');

// Theme

add_theme_support( 'post-thumbnails' );

add_theme_support( 'html5', array('search-form', 'gallery') );

if (function_exists('acf_add_options_page')) {
  acf_add_options_page();
}
