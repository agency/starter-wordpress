<?php
/**
 * Template Tags
 *
 */



/**
 * Excerpt Limit
 * ---------------------------------------------
 * @param $limit the character limit.
 * @param $excerpt the text to shorten
 * @param $full_stop what will be appended to the end.
 * ---------------------------------------------
 **/

function get_excerpt_limit( $limit = 200, $full_stop = '', $excerpt = null ) {

	if(empty($excerpt)){ $excerpt = get_the_excerpt(); }

    if(strlen($excerpt) <= $limit){ return $excerpt . $full_stop; }

	//-- Sized Excerpt
    $x = substr(trim($excerpt),0,$limit);

    //-- Clean The Sized Excerpt so we don't have partial words
    $end_string = strrchr($x, ' ');
    if($end_string != ' ')
    {
        $x = str_replace($end_string,'',$x);
        if(!empty($x))
        {
            $x .= $full_stop;
        }
    }
    else
    {
        $x = rtrim($x);
        $x .= $full_stop;
    }

    return $x;
}

function the_excerpt_limit($limit = 200, $full_stop = '', $excerpt = null) {
    echo get_excerpt_limit($limit, $full_stop, $excerpt);
}

/**
 * Background Image String
 * ---------------------------------------------
 * @param $attachment_id the attachment id
 * @param $size the image size
 * @return background-image:url($src);
 * ---------------------------------------------
 **/

function get_background_image_string($size = 'single-post-thumbnail',$attachment_id = null)
{
    global $post;

    if($attachment_id === null)
    {
        $attachment_id = get_post_thumbnail_id( $post->ID );
    }

    $src = wp_get_attachment_image_src( $attachment_id, $size );
    $src = $src[0];
    if(empty($src))
    {
        $src = get_template_directory_uri().'/assets/img/default-header.jpg';
        // return;
    }
    $string = "background-image:url($src);";
    return $string;
}


function the_background_image_string($size = 'single-post-thumbnail',$attachment_id = null)
{
    $string = get_background_image_string($size,$attachment_id);
    echo $string;
}

/**
 * Attachement Source
 * ---------------------------------------------
 * @param $attachment_id the attachment id
 * @param $size the image size
 * @return image url;
 * ---------------------------------------------
 **/

function get_attachment_src($size = null,$attachment_id)
{
    $src = wp_get_attachment_image_src($attachment_id, $size);
    $src = $src[0];
    return $src;
}

function the_attachment_src($size = null,$attachment_id)
{
    $src = get_attachment_src($size,$attachment_id);
    echo $src;
}


/**
 * ACF button builder
 * ---------------------------------------------
 * @param  $prefix   the acf prefix
 * @param  $class
 * @param  $option   option page buttons
 * @return html
 * ---------------------------------------------
 **/
function the_acf_button( $prefix, $class = '-primary', $option = null ) {
    global $post;

    $id   = $option ? 'option' : $post->ID;
    $text = get_field($prefix.'_button', $id);

    if( get_field($prefix.'_button_type') === 'page' ) {
        $url = get_field($prefix.'_button_page', $id);
    }
    else {
        $url = get_field($prefix.'_button_url', $id);
    }

    $html = '<a class="button '. $class .'" href="'. $url .'">'. $text .'</a>';

    echo $html;
}


function the_acf_link( $prefix, $class = '', $option = null ) {
    global $post;

    $id   = $option ? 'option' : $post->ID;
    $text = get_field($prefix.'_link', $id);

    if( get_field($prefix.'_link_type') === 'page' ) {
        $url = get_field($prefix.'_link_page', $id);
    }
    else {
        $url = get_field($prefix.'_link_url', $id);
    }

    $html = '<a class="link '. $class .'" href="'. $url .'">'. $text .'</a>';

    echo $html;
}




/**
 * Featured Image body class
 * ---------------------------------------------
 * @return classes
 * ---------------------------------------------
 **/
add_action('body_class', 'featured_image_body_class' );
function featured_image_body_class($classes) {
    if ( has_post_thumbnail() ) {
        array_push( $classes, '-has-featured-image' );
    }
    return $classes;
}
