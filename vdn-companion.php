<?php
/**
 * Plugin Name: VDN Companion
 * Description: VDN Companion contient des éléments indispensables au site Voyageurs du Numérique. A utiliser avec le theme VDN-theme
 * Version: 1.1
 * Text Domain: vdn-companion
 * Author: Philippe Sabaty for BSF
 * Author URI: https://www.malt.fr/profile/philippesabaty
 * Tested up to: 4.9.2
 */

/*
 * Debugging settings - TODO : Disable in production mode
 */
// define( 'WP_DEBUG', true );  // to set in /wp_config.php
// define( 'WP_DEBUG_LOG', true ); // to set in /wp_config.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_WARNING | E_PARSE);


require dirname(__FILE__) . '/inc/libs/advanced-custom-fields/acf.php';
define( 'ACF_LITE', true );

/*
 * Create and setup custom post types (Fiche, Club)
 */
require dirname(__FILE__) . '/inc/CPT-club.php';
require dirname(__FILE__) . '/inc/CPT-fiche.php';
require dirname(__FILE__) . '/inc/CPT-tribe_events.php';


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//define ( 'CACHE_DIR', WP_CONTENT_DIR . '/uploads/wp-advanced-pdf/' . $blog_id );


/**
 * Allows usage of URL parameters to set default category/tags in post-new
 * 
 * Original idea from https://gist.github.com/davejamesmiller
 * Found at https://wordpress.stackexchange.com/questions/77504/how-to-add-category-to-wp-admin-post-new-php
 */
add_filter('wp_get_object_terms', function($terms, $object_ids, $taxonomies, $args)
{
    if (!$terms && basename($_SERVER['PHP_SELF']) == 'post-new.php') {

        // Category - note: only 1 category is supported currently
        if ($taxonomies == "'category'" && isset($_REQUEST['category'])) {
            //$id = get_cat_id($_REQUEST['category']);
            $term = get_term_by('slug', $_REQUEST['category'], 'category'); 
            $id = $term->term_id;
            if ($id) {
                return array($id);
            }
        }

        // Tags
        if ($taxonomies == "'post_tag'" && isset($_REQUEST['tags'])) {
            $tags = $_REQUEST['tags'];
            $tags = is_array($tags) ? $tags : explode( ',', trim($tags, " \n\t\r\0\x0B,") );
            $term_ids = array();
            foreach ($tags as $term) {
                if ( !$term_info = term_exists($term, 'post_tag') ) {
                    // Skip if a non-existent term ID is passed.
                    if ( is_int($term) )
                        continue;
                    $term_info = wp_insert_term($term, 'post_tag');
                }
                $term_ids[] = $term_info['term_id'];
            }
            return $term_ids;
        }
    }
    return $terms;
}, 10, 4);



/*
 * Include helpers
 */
require dirname(__FILE__) . '/inc/helpers.php';

/*
 * Add a vdn_fiche_search_form shortcode for /category/fiches-thematiques/...
 */
require dirname(__FILE__) . '/inc/fiche-search-form.php';

/*
 * Add a vdn_club_creation_form shortcode for /fonder-un-club/
 */
require dirname(__FILE__) . '/inc/club-creation-form.php';

/*
 * Add a vdn_club_map shortcode for /clubs
 */
require dirname(__FILE__) . '/inc/club-map-shortcode.php';

/*
 * Add a vdn_event_map shortcode for /events
 */
require dirname(__FILE__) . '/inc/events-map-shortcode.php';

/**
 * Add a Club widget for sidebar
 */
require dirname(__FILE__) . '/inc/monclub-widget.php';


/*
 * Add plugin-specific settings 
 */
add_action('admin_init', 'vdn_companion_admin_init');

function vdn_companion_admin_init(){
    register_setting(
        'general', 
        'vdn_companion_google_api_key'
        //'vdn_companion_google_api_key_validation' 
    );

    add_settings_field(
        'vdn_companion_google_api_key', 
        'Google API Key pour Carte des clubs',
        'vdn_companion_google_api_key_display_callback',
        'general',
        'default'
    );
}

function vdn_companion_google_api_key_display_callback($val){
    echo '<input value="'.get_option('vdn_companion_google_api_key').'" name="vdn_companion_google_api_key"  id="vdn_companion_google_api_key" type="text"  />';
}
?>
