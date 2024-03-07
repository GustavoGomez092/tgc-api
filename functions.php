<?php
require_once('plugin-activation/activate-plugins.php');
if ( is_plugin_active('wp-graphql/wp-graphql.php') ) {
    require_once('graphql.php');
}
/**
 * Headless functions and definitions
 */

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

// add featured images to posts
add_theme_support('post-thumbnails');


// add custom categories at theme activation
add_action( 'after_setup_theme', 'custom_add_cat' );

function custom_add_cat() {

    wp_insert_term(
        'Code', 
        'category', 
        array('slug' => 'code')
    );

    wp_insert_term(
        'Tech', 
        'category', 
        array('slug' => 'tech')
    );

    wp_insert_term(
        'Gaming', 
        'category', 
        array('slug' => 'gaming')
    );

}