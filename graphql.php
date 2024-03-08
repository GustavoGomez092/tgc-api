<?php

// WPGrahQL resolver for the lastest posts

register_graphql_object_type('Notification', [
    'description' => __('notifications object', 'tgc'),
    'fields' => [
        'title' => [
            'type' => 'String',
        ],
        'id' => [
            'type' => 'String',
        ],
        'postId' => [
            'type' => 'String',
        ],
        'excerpt' => [
            'type' => 'String',
        ],
        'date' => [
            'type' => 'String',
        ],
        'image' => [
            'type' => 'String',
        ],
    ]
]);

register_graphql_field('RootQuery', 'notification_center', [
    'type' => ['list_of' => 'Notification'],
    'description' => 'The latest posts for the notification center.',
    'resolve' => function ($source, $args, $context, $info) {
        $query_args = [
            'post_type' => 'post',
            'posts_per_page' => 10,
        ];
        $query = new WP_Query($query_args);

        $data = array ();
        foreach ($query->posts as $post) {
            $data[] = [
                'id' => base64_encode('notification-' . $post->ID),
                'postId' => $post->ID,
                'title' => $post->post_title,
                'excerpt' => $post->post_excerpt,
                'date' => $post->post_date,
                'image' => get_the_post_thumbnail_url($post->ID),
            ];
        }

        return $data;
    },
]);

// register the is featured meta to posts type

add_action('graphql_register_types', function () {
    register_graphql_field('Post', 'featured-post', [
        'type' => 'Boolean',
        'description' => __('Flag to know if the post featured', 'wp-graphql'),
        'resolve' => function ($post) {
            $featured = get_post_meta($post->ID, 'featured-checkbox', true);
            return !empty ($featured) && $featured === 'yes' ? true : false;
        }
    ]);
});

// add new query to get the featured post

// register_graphql_field('RootQuery', 'featuredPost', [
//     'type' => 'Post',
//     'description' => 'Get the featured post of the site.',
//     'resolve' => function ($source, $args, $context, $info) {
//         $args = array (
//             'post_type' => 'post',
//             'meta_query' => array (
//                 array (
//                     'key' => 'featured-checkbox',
//                     'value' => 'yes',
//                 ),
//             ),
//         );
//         $post = null;
//         $query = new WP_Query($args);
//         if ($query->have_posts()) {
//             while ($query->have_posts()) {
//                 $query->the_post();
//                 $post = $query->post;
//             }
//         }
//         return $post;
//     },
// ]);



// First, we register the field in the "where" clause.
add_action('graphql_register_types', function () {

    $customposttype_graphql_single_name = "Post"; // Replace this with your custom post type single name in PascalCase

    // Registering the 'featured' argument in the 'where' clause.
    register_graphql_field('RootQueryTo' . $customposttype_graphql_single_name . 'ConnectionWhereArgs', 'featured', [
        'type' => 'Boolean', // To accept boolean values
        'description' => __('Filter by post objects by featured flag', 'wp-graphql'),
    ]);
});

// Next, we add a filter to modify the query arguments.
add_filter('graphql_post_object_connection_query_args', function ($query_args, $source, $args, $context, $info) {

    if (!array_key_exists('where', $args) || !is_array($args['where']))
        return $query_args;

    if (!array_key_exists('featured', $args['where']))
        return $query_args;

    $featured = $args['where']['featured']; // Accessing the 'featured' argument.

    if (isset ($featured) && $featured === true) {
        $query_args['meta_query'] = [
            [
                'key' => 'featured-checkbox',
                'value' => 'yes',
            ],
        ];
    } elseif (isset ($featured) && $featured === false) {
        $query_args['meta_query'] = [
            [
                'key' => 'featured-checkbox',
                'value' => 'no',
            ],
        ];
    }

    return $query_args;
}, 10, 5);