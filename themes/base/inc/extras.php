<?php
/**
 * Extra Function
 *
 */

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

/**
 * Trace
 */

function trace($val, $title = null)
{
    print "<pre>";
    if($title)
    {
        print "<b>$title</b>\n";
    }
    if(is_array($val))
    {
        print_r($val);
    }
    elseif(is_object($val))
    {
        print var_dump($val);
    }
    else
    {
        print $val;
    }
    print "</pre>";
}


