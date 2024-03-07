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
                'id' => $post->ID,
                'title' => $post->post_title,
                'excerpt' => $post->post_excerpt,
                'date' => $post->post_date,
                'image' => get_the_post_thumbnail_url($post->ID),
            ];
        }

        return $data;
    },
]);