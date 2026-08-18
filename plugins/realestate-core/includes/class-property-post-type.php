<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Property_Post_Type {

    public function __construct() {
        add_action( 'init', array( $this, 'register_post_type' ) );
        add_action( 'init', array( $this, 'register_taxonomies' ) );
    }

    public function register_post_type() {
        $labels = array(
            'name'                  => _x( 'Properties', 'Post type general name', 'realestate-core' ),
            'singular_name'         => _x( 'Property', 'Post type singular name', 'realestate-core' ),
            'menu_name'             => _x( 'Properties', 'Admin Menu text', 'realestate-core' ),
            'add_new'               => __( 'Add New Property', 'realestate-core' ),
            'add_new_item'          => __( 'Add New Property', 'realestate-core' ),
            'edit_item'             => __( 'Edit Property', 'realestate-core' ),
            'new_item'              => __( 'New Property', 'realestate-core' ),
            'view_item'             => __( 'View Property', 'realestate-core' ),
            'view_items'            => __( 'View Properties', 'realestate-core' ),
            'search_items'          => __( 'Search Properties', 'realestate-core' ),
            'not_found'             => __( 'No properties found', 'realestate-core' ),
            'not_found_in_trash'    => __( 'No properties found in Trash', 'realestate-core' ),
            'all_items'             => __( 'All Properties', 'realestate-core' ),
            'archives'              => __( 'Property Archives', 'realestate-core' ),
            'attributes'            => __( 'Property Attributes', 'realestate-core' ),
            'insert_into_item'      => __( 'Insert into property', 'realestate-core' ),
            'uploaded_to_this_item' => __( 'Uploaded to this property', 'realestate-core' ),
            'filter_items_list'     => __( 'Filter properties list', 'realestate-core' ),
            'items_list_navigation' => __( 'Properties list navigation', 'realestate-core' ),
            'items_list'            => __( 'Properties list', 'realestate-core' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_rest'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'property' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-building',
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
            'taxonomies'         => array( 'property_type', 'property_status', 'property_location' ),
        );

        register_post_type( 'property', $args );
    }

    public function register_taxonomies() {

        // Property Type taxonomy.
        register_taxonomy( 'property_type', array( 'property' ), array(
            'labels'            => array(
                'name'              => _x( 'Property Types', 'taxonomy general name', 'realestate-core' ),
                'singular_name'     => _x( 'Property Type', 'taxonomy singular name', 'realestate-core' ),
                'search_items'      => __( 'Search Property Types', 'realestate-core' ),
                'all_items'         => __( 'All Property Types', 'realestate-core' ),
                'parent_item'       => __( 'Parent Property Type', 'realestate-core' ),
                'parent_item_colon' => __( 'Parent Property Type:', 'realestate-core' ),
                'edit_item'         => __( 'Edit Property Type', 'realestate-core' ),
                'update_item'       => __( 'Update Property Type', 'realestate-core' ),
                'add_new_item'      => __( 'Add New Property Type', 'realestate-core' ),
                'new_item_name'     => __( 'New Property Type Name', 'realestate-core' ),
                'menu_name'         => __( 'Property Types', 'realestate-core' ),
            ),
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'property-type' ),
        ) );

        $property_types = array( 'House', 'Apartment', 'Land', 'Commercial Property', 'Office', 'Shop', 'Warehouse', 'Villa' );
        foreach ( $property_types as $type ) {
            if ( ! term_exists( $type, 'property_type' ) ) {
                wp_insert_term( $type, 'property_type' );
            }
        }

        // Property Status taxonomy.
        register_taxonomy( 'property_status', array( 'property' ), array(
            'labels'            => array(
                'name'              => _x( 'Property Statuses', 'taxonomy general name', 'realestate-core' ),
                'singular_name'     => _x( 'Property Status', 'taxonomy singular name', 'realestate-core' ),
                'search_items'      => __( 'Search Property Statuses', 'realestate-core' ),
                'all_items'         => __( 'All Property Statuses', 'realestate-core' ),
                'parent_item'       => __( 'Parent Property Status', 'realestate-core' ),
                'parent_item_colon' => __( 'Parent Property Status:', 'realestate-core' ),
                'edit_item'         => __( 'Edit Property Status', 'realestate-core' ),
                'update_item'       => __( 'Update Property Status', 'realestate-core' ),
                'add_new_item'      => __( 'Add New Property Status', 'realestate-core' ),
                'new_item_name'     => __( 'New Property Status Name', 'realestate-core' ),
                'menu_name'         => __( 'Property Statuses', 'realestate-core' ),
            ),
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'property-status' ),
        ) );

        $property_statuses = array( 'For Sale', 'For Rent' );
        foreach ( $property_statuses as $status ) {
            if ( ! term_exists( $status, 'property_status' ) ) {
                wp_insert_term( $status, 'property_status' );
            }
        }

        // Property Location taxonomy.
        register_taxonomy( 'property_location', array( 'property' ), array(
            'labels'            => array(
                'name'              => _x( 'Locations', 'taxonomy general name', 'realestate-core' ),
                'singular_name'     => _x( 'Location', 'taxonomy singular name', 'realestate-core' ),
                'search_items'      => __( 'Search Locations', 'realestate-core' ),
                'all_items'         => __( 'All Locations', 'realestate-core' ),
                'parent_item'       => __( 'Parent Location', 'realestate-core' ),
                'parent_item_colon' => __( 'Parent Location:', 'realestate-core' ),
                'edit_item'         => __( 'Edit Location', 'realestate-core' ),
                'update_item'       => __( 'Update Location', 'realestate-core' ),
                'add_new_item'      => __( 'Add New Location', 'realestate-core' ),
                'new_item_name'     => __( 'New Location Name', 'realestate-core' ),
                'menu_name'         => __( 'Locations', 'realestate-core' ),
            ),
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'location' ),
        ) );

        $locations = array(
            'Colombo 01', 'Colombo 02', 'Colombo 03', 'Colombo 04', 'Colombo 05',
            'Colombo 06', 'Colombo 07', 'Colombo 08', 'Colombo 09', 'Colombo 10',
            'Colombo 11', 'Colombo 12', 'Colombo 13', 'Colombo 14', 'Colombo 15',
            'Kandy', 'Galle', 'Negombo',
        );
        foreach ( $locations as $location ) {
            if ( ! term_exists( $location, 'property_location' ) ) {
                wp_insert_term( $location, 'property_location' );
            }
        }
    }
}
