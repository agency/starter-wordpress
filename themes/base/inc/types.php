<?php

function base_types() {

    // Example Custom Post TYpe
    // -------------------

    // $labels = array(
    //     'name'               => '[Name]',
    //     'singular_name'      => '[Name]',
    //     'add_new'            => 'Add New',
    //     'add_new_item'       => 'Add New [Name]',
    //     'edit_item'          => 'Edit [Name]',
    //     'new_item'           => 'New [Name]',
    //     'all_items'          => 'All [Name]',
    //     'view_item'          => 'View [Name]',
    //     'search_items'       => 'Search [Name]',
    //     'not_found'          => 'No [name]s found',
    //     'not_found_in_trash' => 'No [name]s found in Trash',
    //     'parent_item_colon'  => '',
    //     'menu_name'          => '[Name]s'
    // );

    // $args = array(
    //     'labels'             => $labels,
    //     'public'             => true,
    //     'publicly_queryable' => true,
    //     'show_ui'            => true,
    //     'show_in_menu'       => true,
    //     'query_var'          => true,
    //     'rewrite'            => array( 'slug' => '[name]' ),
    //     'capability_type'    => 'post',
    //     'has_archive'        => true,
    //     'hierarchical'       => false,
    //     'menu_position'      => 10,
    //     'supports'           => array( 'title', 'editor', 'author' )
    // );

    // register_post_type( '[name]', $args );

}
add_action( 'init', 'base_types' );
