<?php

/*
Plugin Name: Solar Generator Configurator
Description: A WordPress plugin that helps users find the best solar generator solution based on their power needs and generates a complete parts list.
Version: 1.5
Author: Your Name
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Include separate files for modularity
require_once plugin_dir_path(__FILE__) . 'includes/post-types.php';
require_once plugin_dir_path(__FILE__) . 'includes/acf-fields.php';
require_once plugin_dir_path(__FILE__) . 'includes/ajax-handlers.php';
require_once plugin_dir_path(__FILE__) . 'includes/sample-data.php';
require_once plugin_dir_path(__FILE__) . 'includes/frontend-render.php';

class Solar_Generator_Configurator {
    public function __construct() {
        add_shortcode('solar_generator_configurator', array($this, 'render_configurator'));
        add_action('wp_enqueue_scripts', array($this, 'load_assets'));
    }

    public function load_assets() {
        wp_enqueue_style('solar-configurator-style', plugin_dir_url(__FILE__) . 'assets/style.css');
        wp_enqueue_script('solar-configurator-script', plugin_dir_url(__FILE__) . 'assets/script.js', array('jquery'), null, true);
        wp_localize_script('solar-configurator-script', 'solarConfigAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
    }

    public function render_configurator() {
        return render_solar_configurator();
    }
}

new Solar_Generator_Configurator();
