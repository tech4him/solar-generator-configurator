<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * AJAX Handler: Retrieve Solar Generators for Selection
 */
function solar_config_calculate() {
    $generators = get_posts([
        'post_type'      => 'solar_generators',
        'posts_per_page' => -1
    ]);

    if (empty($generators)) {
        echo '<p>No solar generators available.</p>';
        wp_die();
    }

    echo '<h3>Select a Solar Generator:</h3>';
    echo '<select id="selected_generator">';
    foreach ($generators as $generator) {
        echo '<option value="' . esc_attr($generator->ID) . '">' . esc_html($generator->post_title) . '</option>';
    }
    echo '</select>';
    echo '<button id="find_parts">Generate Parts List</button>';
    echo '<div id="solar-config-parts-list"></div>';

    wp_die();
}
add_action('wp_ajax_solar_config_calculate', 'solar_config_calculate');
add_action('wp_ajax_nopriv_solar_config_calculate', 'solar_config_calculate');

/**
 * AJAX Handler: Generate Parts List Based on Selected Generator
 */
function solar_config_generate_parts() {
    $generator_id = isset($_POST['generator_id']) ? intval($_POST['generator_id']) : 0;

    if (!$generator_id) {
        echo '<p>Invalid generator selection.</p>';
        wp_die();
    }

    $solar_input_voltage = get_field('solar_input_voltage', $generator_id);
    $solar_input_wattage = get_field('solar_input_wattage', $generator_id);

    echo '<h3>Recommended Parts List:</h3><ul>';

    // Fetch compatible solar panels
    $args = [
        'post_type'      => 'solar_panels',
        'posts_per_page' => -1,
        'meta_query'     => [
            [
                'key'     => 'solar_panel_voltage',
                'value'   => $solar_input_voltage,
                'type'    => 'NUMERIC',
                'compare' => '<='
            ]
        ]
    ];

    $solar_panels = get_posts($args);
    if (!empty($solar_panels)) {
        foreach ($solar_panels as $panel) {
            echo '<li>' . esc_html($panel->post_title) . '</li>';
        }
    } else {
        echo '<li>No compatible solar panels found.</li>';
    }

    // Add standard accessories
    echo '<li>MC4 Connectors</li>';
    echo '<li>MC4 Extension Cables</li>';
    echo '<li>Anderson or XT90 Adapters</li>';
    echo '<li>Optional: Tilt Mounts</li>';
    echo '</ul>';

    wp_die();
}
add_action('wp_ajax_solar_config_generate_parts', 'solar_config_generate_parts');
add_action('wp_ajax_nopriv_solar_config_generate_parts', 'solar_config_generate_parts');
