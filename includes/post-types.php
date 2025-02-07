<?php 

/**
 * Includes: post-types.php
 */
if (!defined('ABSPATH')) {
    exit;
}

function register_solar_post_types() {
    $post_types = [
        'solar_generators' => 'Solar Generators',
        'solar_panels' => 'Solar Panels',
        'batteries' => 'Batteries',
        'accessories' => 'Accessories'
    ];

    foreach ($post_types as $slug => $name) {
        register_post_type($slug, array(
            'labels' => array(
                'name' => $name,
                'singular_name' => $name
            ),
            'public' => true,
            'has_archive' => true,
            'show_ui' => true,
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields')
        ));
    }
}
add_action('init', 'register_solar_post_types');