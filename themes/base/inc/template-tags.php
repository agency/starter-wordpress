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
 * @param $image_size the image size
 * @return background-image:url($src);
 * ---------------------------------------------
 **/

function get_background_image_string($attachment_id = null, $image_size = 'single-post-thumbnail')
{
    global $post;

    if(empty($attachment_id))
    {
        $attachment_id = get_post_thumbnail_id( $post->ID );
    }

    $src = wp_get_attachment_image_src( $attachment_id, $image_size );
    $src = $src[0];
    if(empty($src))
    {
        $src = get_template_directory_uri().'/assets/img/default-header.jpg';
    }
    $string = "background-image:url($src);";
    return $string;
}


function the_background_image_string($attachment_id = null, $image_size = 'single-post-thumbnail')
{
    $string = get_background_image_string($attachment_id);
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

function get_attachement_src($attachment_id,$size = null)
{
    $src = wp_get_attachment_image_src( $attachment_id, $size );
    $src = $src[0];
    return $src;
}

function the_attachement_src($attachment_id,$size = null)
{
    $src = get_attachement_src($attachment_id,$size);
    echo $src;
}
