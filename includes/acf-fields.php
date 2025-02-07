<?php

/**
 * Includes: acf-fields.php
 */
if (!defined('ABSPATH')) {
    exit;
}

function register_solar_acf_fields() {
    if (function_exists('acf_add_local_field_group')) {
        acf_add_local_field_group(array(
            'key' => 'group_solar_products',
            'title' => 'Solar Product Specifications',
            'fields' => array(
                array(
                    'key' => 'field_generator_output',
                    'label' => 'Generator Output (W)',
                    'name' => 'generator_output',
                    'type' => 'number'
                ),
                array(
                    'key' => 'field_battery_capacity',
                    'label' => 'Battery Capacity (Wh)',
                    'name' => 'battery_capacity',
                    'type' => 'number'
                ),
                array(
                    'key' => 'field_solar_input_voltage',
                    'label' => 'Solar Input Voltage (V)',
                    'name' => 'solar_input_voltage',
                    'type' => 'number'
                ),
                array(
                    'key' => 'field_solar_input_wattage',
                    'label' => 'Solar Input Wattage (W)',
                    'name' => 'solar_input_wattage',
                    'type' => 'number'
                ),
                array(
                    'key' => 'field_solar_panel_wattage',
                    'label' => 'Solar Panel Wattage (W)',
                    'name' => 'solar_panel_wattage',
                    'type' => 'number'
                ),
                array(
                    'key' => 'field_solar_panel_voltage',
                    'label' => 'Solar Panel Voltage (V)',
                    'name' => 'solar_panel_voltage',
                    'type' => 'number'
                )
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'solar_generators'
                    )
                ),
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'solar_panels'
                    )
                ),
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'batteries'
                    )
                )
            )
        ));
    }
}
add_action('acf/init', 'register_solar_acf_fields');