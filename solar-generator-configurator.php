<?php

/*
Plugin Name: Solar Generator Configurator
Description: A WordPress plugin that helps users find the best solar generator solution based on their power needs.
Version: 1.0
Author: Your Name
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Solar_Generator_Configurator {
    public function __construct() {
        add_shortcode('solar_generator_configurator', array($this, 'render_configurator'));
        add_action('wp_enqueue_scripts', array($this, 'load_assets'));
        add_action('init', array($this, 'register_custom_post_types'));
        add_action('init', array($this, 'register_custom_fields'));
        add_action('wp_ajax_solar_config_calculate', array($this, 'calculate_solution'));
        add_action('wp_ajax_nopriv_solar_config_calculate', array($this, 'calculate_solution'));
    }

    public function load_assets() {
        wp_enqueue_style('solar-configurator-style', plugin_dir_url(__FILE__) . 'assets/style.css');
        wp_enqueue_script('solar-configurator-script', plugin_dir_url(__FILE__) . 'assets/script.js', array('jquery'), null, true);
        wp_localize_script('solar-configurator-script', 'solarConfigAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
    }

    public function register_custom_post_types() {
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

    public function register_custom_fields() {
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group(array(
                'key' => 'group_solar_products',
                'title' => 'Solar Product Specifications',
                'fields' => array(
                    array(
                        'key' => 'field_generator_output',
                        'label' => 'Generator Output (W)',
                        'name' => 'generator_output',
                        'type' => 'number',
                        'instructions' => 'Enter the generator\'s power output in watts.',
                    ),
                    array(
                        'key' => 'field_battery_capacity',
                        'label' => 'Battery Capacity (Wh)',
                        'name' => 'battery_capacity',
                        'type' => 'number',
                        'instructions' => 'Enter the battery capacity in watt-hours.',
                    ),
                    array(
                        'key' => 'field_solar_input_voltage',
                        'label' => 'Solar Input Voltage (V)',
                        'name' => 'solar_input_voltage',
                        'type' => 'number',
                        'instructions' => 'Enter the solar input voltage range.',
                    ),
                    array(
                        'key' => 'field_solar_panel_wattage',
                        'label' => 'Solar Panel Wattage (W)',
                        'name' => 'solar_panel_wattage',
                        'type' => 'number',
                        'instructions' => 'Enter the wattage per solar panel.',
                    ),
                    array(
                        'key' => 'field_solar_panel_voltage',
                        'label' => 'Solar Panel Voltage (V)',
                        'name' => 'solar_panel_voltage',
                        'type' => 'number',
                        'instructions' => 'Enter the solar panel voltage.',
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'solar_generators',
                        ),
                    ),
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'solar_panels',
                        ),
                    ),
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'batteries',
                        ),
                    ),
                ),
            ));
        }
    }

    public function calculate_solution() {
        $power_need = isset($_POST['power_need']) ? intval($_POST['power_need']) : 0;
        if ($power_need <= 0) {
            echo '<p>Please enter a valid power requirement.</p>';
            wp_die();
        }

        $results = [];
        $post_types = ['solar_generators', 'solar_panels', 'batteries'];
        foreach ($post_types as $post_type) {
            $args = array(
                'post_type' => $post_type,
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => ($post_type == 'solar_generators') ? 'generator_output' : 'solar_panel_wattage',
                        'value' => $power_need,
                        'type' => 'NUMERIC',
                        'compare' => '>='
                    )
                )
            );
            $results[$post_type] = get_posts($args);
        }

        foreach ($results as $post_type => $solutions) {
            if (!empty($solutions)) {
                echo '<h3>' . ucfirst(str_replace('_', ' ', $post_type)) . ':</h3><ul>';
                foreach ($solutions as $solution) {
                    echo '<li><a href="' . esc_url(get_permalink($solution->ID)) . '">' . esc_html($solution->post_title) . '</a></li>';
                }
                echo '</ul>';
            }
        }

        if (empty(array_filter($results))) {
            echo '<p>No matching solutions found. Try adjusting your power requirement.</p>';
        }
        wp_die();
    }

    public function render_configurator() {
        ob_start();
        ?>
        <div id="solar-configurator">
            <h2>Find Your Solar Generator Solution</h2>
            <form id="solar-config-form">
                <label for="power_need">Enter Your Power Requirement (Watts):</label>
                <input type="number" id="power_need" name="power_need" required>
                <button type="submit">Find My Solution</button>
            </form>
            <div id="solar-config-results"></div>
        </div>
        <script>
        jQuery(document).ready(function($) {
            $('#solar-config-form').on('submit', function(e) {
                e.preventDefault();
                var powerNeed = $('#power_need').val();
                
                $.post(solarConfigAjax.ajaxurl, {
                    action: 'solar_config_calculate',
                    power_need: powerNeed
                }, function(response) {
                    $('#solar-config-results').html(response);
                });
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }
}

new Solar_Generator_Configurator();

function solar_configurator_insert_sample_data() {
    // Check if data already exists
    if (get_option('solar_configurator_sample_data_inserted')) {
        return;
    }

    // Sample data for Solar Generators
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
            'post_title' => $generator['title'],
            'post_type' => 'solar_generators',
            'post_status' => 'publish'
        ]);

        if ($post_id) {
            update_field('generator_output', $generator['output'], $post_id);
            update_field('battery_capacity', $generator['battery'], $post_id);
            update_field('solar_input_voltage', $generator['solar_input'], $post_id);
        }
    }

    // Sample data for Solar Panels
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
            'post_title' => $panel['title'],
            'post_type' => 'solar_panels',
            'post_status' => 'publish'
        ]);

        if ($post_id) {
            update_field('solar_panel_wattage', $panel['wattage'], $post_id);
            update_field('solar_panel_voltage', $panel['voltage'], $post_id);
        }
    }

    // Sample data for Batteries
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
            'post_title' => $battery['title'],
            'post_type' => 'batteries',
            'post_status' => 'publish'
        ]);

        if ($post_id) {
            update_field('battery_capacity', $battery['capacity'], $post_id);
        }
    }

    // Mark sample data as inserted
    update_option('solar_configurator_sample_data_inserted', true);
}
add_action('init', 'solar_configurator_insert_sample_data');

