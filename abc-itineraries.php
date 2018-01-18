<?php
/*
 * Plugin Name: ABC Itineraries
 * Plugin URI: https://github.com/ambassador-baptist-college/abc-itineraries/
 * Description: Evangelist and Travel Group itineraries
 * Version: 1.0.6
 * Author: AndrewRMinion Design
 * Author URI: https://andrewrminion.com
 * GitHub Plugin URI: https://github.com/ambassador-baptist-college/abc-itineraries/
 */

if (!defined('ABSPATH')) {
    exit;
}

CONST ABC_ITINERARIES_PLUGIN_VERSION = '1.0.6';

// Register Custom Post Type
function itinerary_post_type() {

    $labels = array(
        'name'                  => 'Meeting',
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
    $rewrite = array(
        'slug'                  => 'meetings',
        'with_front'            => true,
        'pages'                 => true,
        'feeds'                 => true,
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
        'rewrite'               => $rewrite,
        'capability_type'       => 'page',
    );
    register_post_type( 'meeting', $args );

}
add_action( 'init', 'itinerary_post_type', 0 );

// Register Custom Taxonomy
function meeting_categories() {

    $labels = array(
        'name'                       => 'Groups',
        'singular_name'              => 'Group',
        'menu_name'                  => 'Group Categories',
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
    $rewrite = array(
        'slug'                       => 'meetings',
        'with_front'                 => true,
        'hierarchical'               => true,
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'rewrite'                    => $rewrite,
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
    if ( 'meeting' == $query->get( 'post_type' ) || $query->is_tax( 'group-name' ) ) {
        $query->set( 'posts_per_page', -1 );
        $query->set( 'meta_query', array(
            'begin_date' => array(
                'key'       => 'begin_date',
                'value'     => date( 'Ymd' ),
                'compare'   => '>=',
            ),
            'am_pm' => array(
                'key'       => 'am_pm',
            ),
        ));
        $query->set( 'orderby', array(
            'begin_date' => 'ASC',
            'am_pm'      => 'ASC',
        ));
    }
}
add_filter( 'pre_get_posts', 'sort_meetings' );

// Add settings page
add_action( 'admin_menu', 'abc_itineraries_add_admin_menu' );
add_action( 'admin_init', 'abc_itineraries_settings_init' );

// Add to menu
function abc_itineraries_add_admin_menu() {
    add_submenu_page( 'edit.php?post_type=meeting', 'ABC Itineraries', 'Settings', 'manage_options', 'abc-itineraries', 'abc_itineraries_options_page' );
}

// Add settings section and fields
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

// Print API Key field
function abc_itineraries_api_key_render() {
    $options = get_option( 'abc_itineraries_settings' ); ?>
    <input type="text" name="abc_itineraries_settings[abc_itineraries_api_key]" placeholder="AIzaSyD4iE2xVSpkLLOXoyqT-RuPwURN3ddScAI" size="45" value="<?php echo $options['abc_itineraries_api_key']; ?>">
    <?php
}

// Print API settings description
function abc_itineraries_api_settings_section_callback() {
    echo __( 'Enter your API Keys below. Don’t have it? <a href="https://console.developers.google.com/" target="_blank">Get it here on the Google Developers Console</a>.', 'abc_itineraries' );
}

// Print form
function abc_itineraries_options_page() { ?>
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

// Use API Key for ACF
add_action('acf/init', 'abc_acf_init');
function abc_acf_init() {
    $options = get_option( 'abc_itineraries_settings' );
    acf_update_setting('google_api_key', $options['abc_itineraries_api_key'] );
}

// Add custom column headers to admin
function abc_itineraries_custom_columns( $columns ) {
    $custom_columns = array();

    foreach ( $columns as $key => $title ) {
        if ( 'date' == $key ) {
            $custom_columns['pastor_name'] = 'Pastor Name';
            $custom_columns['begin_date'] = 'Begin Date';
            $custom_columns['location'] = 'Location';
            $custom_columns['date'] = 'Publish Date';
        } else {
            $custom_columns[$key] = $title;
        }
    }

    return $custom_columns;
}
add_filter( 'manage_edit-meeting_columns', 'abc_itineraries_custom_columns' );

// Add custom column content to admin
function abc_itineraries_custom_column_content( $column, $post_id ) {
    global $post;

    switch ( $column ) {
        case 'pastor_name' :
            the_field( 'pastor_name' );
            break;

        case 'begin_date' :
            echo get_field( 'begin_date' ) . ' ' . ( is_array( get_field( 'am_pm' ) ) ? implode( ', ', get_field( 'am_pm' ) ) : '' );
            break;

        case 'location' :
            if ( get_field( 'address_1' ) ) {
                echo get_field( 'address_1' ) . '<br/>';
            }
            if ( get_field( 'address_2' ) ) {
                echo get_field( 'address_2' ) . '<br/>';
            }
            echo get_field( 'city' ) . ', ' . get_field( 'state' );
            break;
    }
}
add_action( 'manage_meeting_posts_custom_column', 'abc_itineraries_custom_column_content', 10, 2 );

// Make columns sortable
function abc_itineraries_sortable_columns( $columns ) {
    $columns['begin_date'] = 'begin_date';

    return $columns;
}
add_filter( 'manage_edit-meeting_sortable_columns', 'abc_itineraries_sortable_columns' );

// Register frontend scripts and styles
function register_google_map() {
    wp_register_script( 'google-map-api', 'https://maps.googleapis.com/maps/api/js?key=' . get_option( 'abc_itineraries_settings' )['abc_itineraries_api_key'] . '&amp;callback=initMap', array( 'abc-itineraries-map' ), NULL, true );
    wp_register_script( 'abc-itineraries-map', plugins_url( 'js/initializeMap.min.js', __FILE__ ), array( 'jquery' ), ABC_ITINERARIES_PLUGIN_VERSION, true );
    wp_register_style( 'abc-itineraries-map', plugins_url( 'css/abc-itineraries-map.css', __FILE__ ), array(), ABC_ITINERARIES_PLUGIN_VERSION );
}
add_action( 'wp_enqueue_scripts', 'register_google_map' );

// Register backend script
function register_backend_js() {
    global $post_type;
    if ( 'meeting' == $post_type ) {
        wp_enqueue_script( 'abc-itineraries-backend', plugins_url( 'js/backend.min.js', __FILE__ ), array( 'jquery' ), ABC_ITINERARIES_PLUGIN_VERSION );
    }
}
add_action( 'admin_enqueue_scripts', 'register_backend_js' );

// Use the same slug for post type and taxonomy
function generate_meeting_taxonomy_rewrite_rules( $wp_rewrite ) {
    $rules = array();
    $post_types = get_post_types( array( 'public' => true, '_builtin' => false ), 'objects' );
    $taxonomies = get_taxonomies( array( 'public' => true, '_builtin' => false ), 'objects' );

    foreach ( $post_types as $post_type ) {
        $post_type_name = $post_type->name; // 'developer'
        $post_type_slug = $post_type->rewrite['slug']; // 'developers'

        foreach ( $taxonomies as $taxonomy ) {
            if ( $taxonomy->object_type[0] == $post_type_name ) {
                $terms = get_categories( array( 'type' => $post_type_name, 'taxonomy' => $taxonomy->name, 'hide_empty' => 0 ) );
                foreach ( $terms as $term ) {
                    $rules[$post_type_slug . '/' . $term->slug . '/?$'] = 'index.php?' . $term->taxonomy . '=' . $term->slug;
                }
            }
        }
    }
    $wp_rewrite->rules = $rules + $wp_rewrite->rules;
}
add_action('generate_rewrite_rules', 'generate_meeting_taxonomy_rewrite_rules');

// Modify the page title
function filter_meeting_page_title( $title, $id = NULL ) {
    if ( is_post_type_archive( 'meeting' ) ) {
        $title = 'Meetings';
    }

    return $title;
}
add_filter( 'custom_title', 'filter_meeting_page_title' );

// Add shortcode
function abc_itinerary_shortcode( $attributes ) {
    $shortcode_attributes = shortcode_atts( array (
        'category' => NULL,
    ), $attributes );
    $terms = explode( ',', esc_attr( $shortcode_attributes['category'] ) );

    // set up query args
    $itinerary_query_args = array(
        'post_type'         => 'meeting',
        'posts_per_page'    => -1,
        'tax_query'         => array(
            array(
                'taxonomy'  => 'group-name',
                'field'     => 'term_id',
                'terms'     => $terms,
            ),
        ),
    );

    ob_start();
    global $wp_query;
    $original_query = $wp_query;
    $wp_query = new WP_Query( $itinerary_query_args );

    if ( $wp_query->have_posts() ) {
        // don’t show group name if only one is set in the shortcode
        if ( count( $terms ) == 1 ) {
            $single_term = true;
        }

        include( 'includes/map-and-table.php' );
    }
    wp_reset_postdata();
    $wp_query = $original_query;

    return ob_get_clean();
}
add_shortcode( 'abc_itinerary', 'abc_itinerary_shortcode' );
