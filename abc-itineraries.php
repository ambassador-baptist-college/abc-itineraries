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
        'has_archive'           => 'meetings',
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
    register_taxonomy( 'group-name', array( 'meeting' ), $args );

}
add_action( 'init', 'meeting_categories', 0 );

// Add custom archive template
function get_meeting_archive_template( $archive_template ) {
     global $post;
     if ( is_post_type_archive ( 'meeting' ) || is_tax( 'group-name' ) ) {
          $archive_template = dirname( __FILE__ ) . '/archive-meeting.php';
     }
     return $archive_template;
}
add_filter( 'archive_template', 'get_meeting_archive_template' ) ;
add_filter( 'taxonomy_archive', 'get_meeting_archive_template' ) ;

// Sort by beginning date ascending
function sort_meetings( $query ) {
    if ( ( isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'meeting' ) || ( isset($query->query_vars['group-name']) ) ) {
        $query->set( 'posts_per_page', -1 );
        $query->set( 'orderby', 'meta_value_num' );
        $query->set( 'meta_key', 'begin_date' );
        $query->set( 'order', 'ASC' );
        $query->set( 'meta_query', array(
            array(
                'key'       => 'begin_date',
                'value'     => date( 'Y-m-d' ),
                'compare'   => '>=',
            ),
        ));
    }
}
add_filter( 'pre_get_posts', 'sort_meetings' );

// Add settings page
add_action( 'admin_menu', 'abc_itineraries_add_admin_menu' );
add_action( 'admin_init', 'abc_itineraries_settings_init' );

// add to menu
function abc_itineraries_add_admin_menu() {
    add_submenu_page( 'edit.php?post_type=meeting', 'ABC Itineraries', 'Settings', 'manage_options', 'abc-itineraries', 'abc_itineraries_options_page' );
}

// add settings section and fields
function abc_itineraries_settings_init() {
    register_setting( 'abc_itineraries_options', 'abc_itineraries_settings' );

    // API settings
    add_settings_section(
        'abc_itineraries_options_keys_section',
        __( 'Add your Google API Key', 'abc_itineraries' ),
        'abc_itineraries_api_settings_section_callback',
        'abc_itineraries_options'
    );

    add_settings_field(
        'abc_itineraries_api_key',
        __( 'Google API Key', 'abc_itineraries' ),
        'abc_itineraries_api_key_render',
        'abc_itineraries_options',
        'abc_itineraries_options_keys_section'
    );
}

// print API Key field
function abc_itineraries_api_key_render() {
    $options = get_option( 'abc_itineraries_settings' ); ?>
    <input type="text" name="abc_itineraries_settings[abc_itineraries_api_key]" placeholder="AIzaSyD4iE2xVSpkLLOXoyqT-RuPwURN3ddScAI" size="45" value="<?php echo $options['abc_itineraries_api_key']; ?>">
    <?php
}

// print API settings description
function abc_itineraries_api_settings_section_callback(  ) {
    echo __( 'Enter your API Keys below. Don’t have it? <a href="https://console.developers.google.com/" target="_blank">Get it here on the Google Developers Console</a>.', 'abc_itineraries' );
}

// print form
function abc_itineraries_options_page(  ) { ?>
    <div class="wrap">
       <h2>Google Maps Embed API Key</h2>
        <form action="options.php" method="post">

            <?php
            settings_fields( 'abc_itineraries_options' );
            do_settings_sections( 'abc_itineraries_options' );
            submit_button();
            ?>

        </form>
    </div>
    <?php
}

// Register frontend scripts and styles
function register_google_map() {
    wp_register_script( 'google-map-api', 'https://maps.googleapis.com/maps/api/js?key=' . get_option( 'abc_itineraries_settings' )['abc_itineraries_api_key'] . '&amp;callback=initMap', array( 'abc-itineraries-map' ), NULL, true );
    wp_register_script( 'abc-itineraries-map', plugins_url( 'js/initializeMap.min.js', __FILE__ ), array( 'jquery' ), NULL, true );
    wp_register_style( 'abc-itineraries-map', plugins_url( 'css/abc-itineraries-map.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'register_google_map' );

// Register backend script
function register_backend_js() {
    wp_enqueue_script( 'abc-itineraries-backend', plugins_url( 'js/backend.min.js', __FILE__ ), array( 'jquery' ) );
}
add_action( 'admin_enqueue_scripts', 'register_backend_js' );
