<?php

/**
 * Filters wp_title to print a nicer title. Based on _s_wp_title from _underscores.
 */

function base_wp_title( $title, $sep ) {
    global $page, $paged;

    if ( is_feed() ) {
    	return $title;
    }

    // Add the blog name
    $title .= get_bloginfo( 'name' );

    // Add the blog description for the home/front page.
    $site_description = get_bloginfo( 'description', 'display' );
    if ( $site_description && ( is_home() || is_front_page() ) ) {
    	$title .= " $sep $site_description";
    }

    // Add a page number if necessary:
    if ( $paged >= 2 || $page >= 2 ) {
    	$title .= " $sep " . sprintf( __( 'Page %s', '_s' ), max( $paged, $page ) );
    }

    return $title;
}
add_filter( 'wp_title', 'base_wp_title', 10, 2 );


/**
 * Remove inline styles from images
 */

add_shortcode('wp_caption', 'fixed_img_caption_shortcode');
add_shortcode('caption', 'fixed_img_caption_shortcode');

function fixed_img_caption_shortcode($attr, $content = null) {
    // New-style shortcode with the caption inside the shortcode with the link and image tags.
    if ( ! isset( $attr['caption'] ) ) {
        if ( preg_match( '#((?:<a [^>]+>\s*)?<img [^>]+>(?:\s*</a>)?)(.*)#is', $content, $matches ) ) {
            $content = $matches[1];
            $attr['caption'] = trim( $matches[2] );
        }
    }

    // Allow plugins/themes to override the default caption template.
    $output = apply_filters('img_caption_shortcode', '', $attr, $content);
    if ( $output != '' )
        return $output;

    extract(shortcode_atts(array(
        'id'    => '',
        'align' => 'alignnone',
        'width' => '',
        'caption' => ''
    ), $attr));

    if ( 1 > (int) $width || empty($caption) )
        return $content;

    if ( $id ) $id = 'id="' . esc_attr($id) . '" ';

    return '<div ' . $id . 'class="wp-caption ' . esc_attr($align) . '">'
    . do_shortcode( $content ) . '<p class="wp-caption-text">' . $caption . '</p></div>';
}

/**
 * Get first image from post
 */

function get_first_image($size = false) {

    global $post, $_wp_additional_image_sizes;
    $first_img = '';

    $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
    $first_img = $matches[1][0];

    if (empty($first_img)) {
        return;
    }

    if ($size && $_wp_additional_image_sizes[$size]['crop'] == 1) {
        $size = '-' . $_wp_additional_image_sizes[$size]['width'] . 'x' . $_wp_additional_image_sizes[$size]['height'] . '.jpg';
        $pattern = '/-\d+x\d+\.jpg$/i';
        $first_img = preg_replace($pattern, $size, $first_img);
    }

    return $first_img;

}

/**
 * Get the page title (considering categories etc)
 */

function get_display_title() {

    global $post;

    $title = false;

    if (is_page()) {
        $title = get_the_title();
    } elseif (is_single()) {
        if (get_post_type() == 'profile') {
            $title = get_the_title(7455);
        } elseif (get_post_type() == 'post') {
            $title = get_the_title(get_option('page_for_posts'));
        } elseif (get_post_type() == 'opportunity') {
            $opps_page = get_field('opportunities_listing_page','option');
            $title = $opps_page->post_title;
        } else {
            $title = get_the_title();
        }
    } elseif (is_home()) {
        $title = get_the_title(get_option('page_for_posts'));
    } elseif (is_category()) {
        $title = single_cat_title('', false);
    } elseif (is_tag()) {
        $title = 'Tag Archives';
    } elseif (is_tax('organisation')) {
        $tax = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
        $title = strip_tags(term_description($tax->term_id, get_query_var( 'taxonomy' )));
    } elseif (is_author()) {
        $title = get_the_author_meta('display_name', $post->post_author);
    } elseif (is_archive()) {
        $title = get_the_title(get_option('page_for_posts')) . ' Archives';
    } elseif (is_search()) {
        $title = 'Search Results';
    } elseif (is_404()) {
        $title = 'Nothing Found';
    }

    return $title;

}

/**
 * Get the parent ID (with provisions for sections)
 */

function get_parent_id() {

    global $post;

    if (get_post_type() == 'profile') {
        $parent_obj = get_page(7455);
        return $parent_obj->post_parent;
    } elseif (is_single() && get_post_type() == 'opportunity') {
        $opps_page = get_field('opportunities_listing_page','option');
        return $opps_page->post_parent;
    }

    return $post->post_parent;

}

function get_tree_id() {
    global $post;
    $post_ref = (get_parent_id() > 0) ? get_parent_id() : $post->ID;

    return $post_ref;
}

/**
 * Get the parent title
 */

function get_parent_title() {

    global $post;

    $parent = get_parent_id();

    if ($parent > 0) {
        return get_the_title($parent);
    } elseif (is_single() && get_post_type() == 'opportunity') {
        $opps_page = get_field('opportunities_listing_page','option');
        return get_the_title($opps_page->post_parent);
    } elseif (is_single() && get_post_type() == 'event' && !get_field('advanced_layout')) {

        // get location
        $terms = get_the_terms($post->ID, 'organisation');
        $locations = '';
        $i = 0;
        foreach ($terms as $term) {
            $locations .= ($i > 0) ? ',' . strip_tags(term_description($term->term_id, 'organisation')) : strip_tags(term_description($term->term_id, 'organisation'));
            $i++;
        }

        $opps_page = get_field('programs_listing_page','option');
        return get_the_title($opps_page->ID) . ' - ' . $locations;
    } elseif (is_archive()) {
        return get_the_title(get_option('page_for_posts'));
    } else {
        return false;
    }

}

/**
 * Get the parent permalink
 */

function get_parent_url() {

    global $post;

    $parent = get_parent_id();

    if ($parent > 0) {
        return get_permalink($parent);
    } elseif (is_single() && get_post_type() == 'opportunity') {
        $opps_page = get_field('opportunities_listing_page','option');
        return get_permalink($opps_page->post_parent);
    } elseif (is_single() && get_post_type() == 'event') {
        $opps_page = get_field('programs_listing_page','option');
        return get_permalink($opps_page->ID);
    } elseif (is_archive()) {
        return get_permalink(get_option('page_for_posts'));
    } else {
        return false;
    }

}


/**
 * Check if there's a submenu
 */

function has_submenu() {

    global $post;
    $post_ref = get_tree_id();

    $parents = array();

    $locations = get_nav_menu_locations();
    $menu = wp_get_nav_menu_object($locations['primary']);
    $menu_items = wp_get_nav_menu_items($menu->term_id);
    foreach( $menu_items as $menu_item ) {
        if ($menu_item->menu_item_parent != 0) {
            $parents[] = $menu_item->menu_item_parent;
        }
    }

    foreach($parents as $parent) {
        $meta = get_post_meta($parent, '_menu_item_object_id', true);
        if ($meta == $post_ref) return true;
    }

    return false;

}


/**
 * Check if date is in the future
 */

function is_future($date) {
    return strtotime($date) >= strtotime(date('Ymd'));
}


/**
 * Get page description
 */

function the_description() {

    global $post;
    setup_postdata($post);

    $excerpt = get_the_excerpt();

    if (!$excerpt || is_front_page()) {
        echo get_field('default_facebook_description', 'option');
    } else {
        echo strip_tags($excerpt);
    }

    wp_reset_postdata();

}

/**
 * Get page preview image (facebook)
 */

function the_preview_image() {

    global $post;

    if (has_post_thumbnail() && !is_front_page()) {
        $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'facebook');
        echo $thumb[0];
    } elseif (get_first_image() && !is_front_page()) {
        echo get_first_image('facebook');
    } else {
        echo get_bloginfo('template_directory') . '/assets/img/facebook.jpg';
    }

}
