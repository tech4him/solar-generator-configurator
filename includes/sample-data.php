<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

function insert_sample_data() {
    // Prevent duplicate insertion
    if (get_option('solar_configurator_sample_data_inserted')) {
        return;
    }

    // Sample Solar Generators
    $generators = [
        [
            'title' => 'Bluetti AC200P',
            'output' => 2000,
            'battery' => 2000,
            'solar_input' => 700
        ],
        [
            'title' => 'EcoFlow Delta 1300',
            'output' => 1800,
            'battery' => 1260,
            'solar_input' => 400
        ]
    ];

    foreach ($generators as $generator) {
        $post_id = wp_insert_post([
            'post_title'   => $generator['title'],
            'post_type'    => 'solar_generators',
            'post_status'  => 'publish'
        ]);

        if ($post_id) {
            update_field('generator_output', $generator['output'], $post_id);
            update_field('battery_capacity', $generator['battery'], $post_id);
            update_field('solar_input_wattage', $generator['solar_input'], $post_id);
        }
    }

    // Sample Solar Panels
    $panels = [
        [
            'title' => 'Renogy 100W Monocrystalline Panel',
            'wattage' => 100,
            'voltage' => 18
        ],
        [
            'title' => 'Jackery SolarSaga 200W',
            'wattage' => 200,
            'voltage' => 20
        ]
    ];

    foreach ($panels as $panel) {
        $post_id = wp_insert_post([
            'post_title'   => $panel['title'],
            'post_type'    => 'solar_panels',
            'post_status'  => 'publish'
        ]);

        if ($post_id) {
            update_field('solar_panel_wattage', $panel['wattage'], $post_id);
            update_field('solar_panel_voltage', $panel['voltage'], $post_id);
        }
    }

    // Sample Batteries
    $batteries = [
        [
            'title' => 'Goal Zero Yeti 1000X',
            'capacity' => 983
        ],
        [
            'title' => 'Bluetti B230 Expansion Battery',
            'capacity' => 2048
        ]
    ];

    foreach ($batteries as $battery) {
        $post_id = wp_insert_post([
            'post_title'   => $battery['title'],
            'post_type'    => 'batteries',
            'post_status'  => 'publish'
        ]);

        if ($post_id) {
            update_field('battery_capacity', $battery['capacity'], $post_id);
        }
    }

    // Set flag to prevent re-insertion
    update_option('solar_configurator_sample_data_inserted', true);
}

// Hook into WordPress
add_action('init', 'insert_sample_data');
