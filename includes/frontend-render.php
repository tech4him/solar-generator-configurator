<?php


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Render the Solar Generator Configurator
 */
function render_solar_configurator() {
    ob_start();
    ?>
    <div id="solar-configurator">
        <h2>Find Your Solar Generator Solution</h2>
        <button id="find_generator">Find My Solution</button>
        <div id="solar-config-results"></div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Fetch available solar generators
        $('#find_generator').on('click', function(e) {
            e.preventDefault();
            $.post(solarConfigAjax.ajaxurl, {
                action: 'solar_config_calculate'
            }, function(response) {
                $('#solar-config-results').html(response);
            });
        });

        // Fetch compatible parts list when generator is selected
        $(document).on('click', '#find_parts', function(e) {
            e.preventDefault();
            var generatorId = $('#selected_generator').val();
            $.post(solarConfigAjax.ajaxurl, {
                action: 'solar_config_generate_parts',
                generator_id: generatorId
            }, function(response) {
                $('#solar-config-parts-list').html(response);
            });
        });
    });
    </script>

    <?php
    return ob_get_clean();
}
