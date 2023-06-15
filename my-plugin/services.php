<?php
    // Register Custom Post Type Services
function create_services_cpt() {

	$labels = array(
		'name' => _x( 'Custom Posts', 'Post Type General Name', 'textdomain' ),
		'singular_name' => _x( 'Services', 'Post Type Singular Name', 'textdomain' ),
		'menu_name' => _x( 'Custom Posts', 'Admin Menu text', 'textdomain' ),
		'name_admin_bar' => _x( 'Services', 'Add New on Toolbar', 'textdomain' ),
		'archives' => __( 'Services Archives', 'textdomain' ),
		'attributes' => __( 'Services Attributes', 'textdomain' ),
		'parent_item_colon' => __( 'Parent Services:', 'textdomain' ),
		'all_items' => __( 'All Custom Posts', 'textdomain' ),
		'add_new_item' => __( 'Add New Services', 'textdomain' ),
		'add_new' => __( 'Add New', 'textdomain' ),
		'new_item' => __( 'New Services', 'textdomain' ),
		'edit_item' => __( 'Edit Services', 'textdomain' ),
		'update_item' => __( 'Update Services', 'textdomain' ),
		'view_item' => __( 'View Services', 'textdomain' ),
		'view_items' => __( 'View Custom Posts', 'textdomain' ),
		'search_items' => __( 'Search Services', 'textdomain' ),
		'not_found' => __( 'Not found', 'textdomain' ),
		'not_found_in_trash' => __( 'Not found in Trash', 'textdomain' ),
		'featured_image' => __( 'Featured Image', 'textdomain' ),
		'set_featured_image' => __( 'Set featured image', 'textdomain' ),
		'remove_featured_image' => __( 'Remove featured image', 'textdomain' ),
		'use_featured_image' => __( 'Use as featured image', 'textdomain' ),
		'insert_into_item' => __( 'Insert into Services', 'textdomain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Services', 'textdomain' ),
		'items_list' => __( 'Custom Posts list', 'textdomain' ),
		'items_list_navigation' => __( 'Custom Posts list navigation', 'textdomain' ),
		'filter_items_list' => __( 'Filter Custom Posts list', 'textdomain' ),
	);
	$args = array(
		'label' => __( 'Services', 'textdomain' ),
		'description' => __( 'This is services for custom post type with category and tags', 'textdomain' ),
		'labels' => $labels,
		'menu_icon' => '',
		'supports' => array('title', 'editor', 'excerpt', 'thumbnail'),
		'taxonomies' => array(),
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 5,
		'show_in_admin_bar' => true,
		'show_in_nav_menus' => true,
		'can_export' => true,
		'has_archive' => true,
		'hierarchical' => false,
		'exclude_from_search' => false,
		'show_in_rest' => true,
		'publicly_queryable' => true,
		'capability_type' => 'post',
	);
	register_post_type( 'services', $args );

}
add_action( 'init', 'create_services_cpt', 0 );
?>