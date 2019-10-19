<?php

/*
* Creating a function to create our CPT
*/

function branch_post_type() {

// Set UI labels for Custom Post Type
    $labels = array(
        'name'                => _x( 'Sucursales', 'Post Type General Name', 'branchs' ),
        'singular_name'       => _x( 'Sucursal', 'Post Type Singular Name', 'branchs' ),
        'menu_name'           => __( 'Sucursales', 'branchs' ),
        'parent_item_colon'   => __( 'Parent Sucursal', 'branchs' ),
        'all_items'           => __( 'All Sucursales', 'branchs' ),
        'view_item'           => __( 'View Sucursal', 'branchs' ),
        'add_new_item'        => __( 'Add New Sucursal', 'branchs' ),
        'add_new'             => __( 'Add New', 'branchs' ),
        'edit_item'           => __( 'Edit Sucursal', 'branchs' ),
        'update_item'         => __( 'Update Sucursal', 'branchs' ),
        'search_items'        => __( 'Search Sucursal', 'branchs' ),
        'not_found'           => __( 'Not Found', 'branchs' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'branchs' ),
    );

// Set other options for Custom Post Type

    $args = array(
        'label'               => __( 'branchs', 'branchs' ),
        'description'         => __( 'Sucursal news and reviews', 'branchs' ),
        'labels'              => $labels,
        // Features this CPT supports in Post Editor
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
        // You can associate this CPT with a taxonomy or custom taxonomy.
        /* A hierarchical CPT is like Pages and can have
        * Parent and child items. A non-hierarchical CPT
        * is like Posts.
        */
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );

    // Registering your Custom Post Type
    register_post_type( 'branchs', $args );

}

/* Hook into the 'init' action so that the function
* Containing our post type registration is not
* unnecessarily executed.
*/

add_action( 'init', 'branch_post_type', 0 );
