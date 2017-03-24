<?php

function base_types() {

    // Example Custom Post TYpe
    // -------------------

    $labels = array(
        'name'               => 'Council',
        'singular_name'      => 'Council',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Council',
        'edit_item'          => 'Edit Council',
        'new_item'           => 'New Council',
        'all_items'          => 'All Council',
        'view_item'          => 'View Council',
        'search_items'       => 'Search Council',
        'not_found'          => 'No councils found',
        'not_found_in_trash' => 'No councils found in Trash',
        'parent_item_colon'  => '',
        'menu_name'          => 'Councils'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'council' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 10,
        'supports'           => array( 'title', 'editor', 'author' )
    );

    register_post_type( 'council', $args );

}
// add_action( 'init', 'base_types' );
