<?php
require_once('plugin-activation/activate-plugins.php');
/**
 * Headless functions and definitions
 */

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

// add featured images to posts
add_theme_support('post-thumbnails');

if ( in_array( 'wp-graphql/wp-graphql.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
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


    // WPGrahQL resolver for the lastest posts

    register_graphql_object_type( 'Notification', [
        'description' => __( 'notifications object', 'tgc' ),
        'fields'      => [
            'title'   => [
                'type' => 'String',
            ],
            'excerpt' => [
                'type' => 'String',
            ],
            'date'    => [
                'type' => 'String',
            ],
            'image'  => [
                'type' => 'String',
            ],
        ]
    ] );

    register_graphql_field('RootQuery', 'notification_center', [
        'type' => ['list_of' => 'Notification'],
        'description' => 'The latest posts for the notification center.',
        'resolve' => function ($source, $args, $context, $info) {
            $query_args = [
                'post_type' => 'post',
                'posts_per_page' => 10,
            ];
            $query = new WP_Query($query_args);

            $data = array();
            foreach ($query->posts as $post) {
                $data[] = [
                    'title'   => $post->post_title,
                    'excerpt' => $post->post_excerpt,
                    'date'    => $post->post_date,
                    'image'   => get_the_post_thumbnail_url($post->ID),
                ];
            }

            return $data;
        },
    ]);
}