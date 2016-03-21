<?php
/*
 * Plugin Name: ABC Itineraries
 * Plugin URI: https://gist.github.com/macbookandrew/92f01a1be124cd2678f0
 * Description: Evangelist and Travel Group itineraries
 * Version: 1.0.0
 * Author: AndrewRMinion Design
 * Author URI: https://andrewrminion.com
 */

if (!defined('ABSPATH')) {
    exit;
}

// Register Custom Post Type
function itinerary() {

    $labels = array(
        'name'                  => 'Meetings',
        'singular_name'         => 'Meeting',
        'menu_name'             => 'Meetings',
        'name_admin_bar'        => 'Meetings',
        'archives'              => 'Meeting Archives',
        'parent_item_colon'     => 'Parent Meeting:',
        'all_items'             => 'All Meetings',
        'add_new_item'          => 'Add New Meeting',
        'add_new'               => 'Add New',
        'new_item'              => 'New Meeting',
        'edit_item'             => 'Edit Meeting',
        'update_item'           => 'Update Meeting',
        'view_item'             => 'View Meeting',
        'search_items'          => 'Search Meeting',
        'not_found'             => 'Not found',
        'not_found_in_trash'    => 'Not found in Trash',
        'featured_image'        => 'Featured Image',
        'set_featured_image'    => 'Set featured image',
        'remove_featured_image' => 'Remove featured image',
        'use_featured_image'    => 'Use as featured image',
        'insert_into_item'      => 'Insert into meeting',
        'uploaded_to_this_item' => 'Uploaded to this meeting',
        'items_list'            => 'Meetings list',
        'items_list_navigation' => 'Meetings list navigation',
        'filter_items_list'     => 'Filter meetings list',
    );
    $args = array(
        'label'                 => 'Meeting',
        'description'           => 'Evangelist and Travel Group Meetings',
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'author', 'thumbnail', 'custom-fields', 'page-attributes', ),
        'taxonomies'            => array( 'category', 'post_tag', 'group' ),
        'hierarchical'          => true,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 20,
        'menu_icon'             => 'dashicons-location-alt',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => 'resources/traveling-groups/all-meetings',
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
    );
    register_post_type( 'meeting', $args );

}
add_action( 'init', 'itinerary', 0 );

// Register Custom Taxonomy
function meeting_categories() {

    $labels = array(
        'name'                       => 'Groups',
        'singular_name'              => 'Group',
        'menu_name'                  => 'Group',
        'all_items'                  => 'All Groups',
        'parent_item'                => 'Parent Group',
        'parent_item_colon'          => 'Parent Group:',
        'new_item_name'              => 'New Group Name',
        'add_new_item'               => 'Add New Group',
        'edit_item'                  => 'Edit Group',
        'update_item'                => 'Update Group',
        'view_item'                  => 'View Group',
        'separate_items_with_commas' => 'Separate groups with commas',
        'add_or_remove_items'        => 'Add or remove groups',
        'choose_from_most_used'      => 'Choose from the most used',
        'popular_items'              => 'Popular Groups',
        'search_items'               => 'Search Groups',
        'not_found'                  => 'Not Found',
        'no_terms'                   => 'No groups',
        'items_list'                 => 'Groups list',
        'items_list_navigation'      => 'Groups list navigation',
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );
    register_taxonomy( 'itinerary_category', array( 'meeting' ), $args );

}
add_action( 'init', 'meeting_categories', 0 );

// Add custom archive template
function get_meeting_archive_template( $archive_template ) {
     global $post;
     if ( is_post_type_archive ( 'meeting' ) ) {
          $archive_template = dirname( __FILE__ ) . '/archive-meeting.php';
     }
     return $archive_template;
}
add_filter( 'archive_template', 'get_meeting_archive_template' ) ;
