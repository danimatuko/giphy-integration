<?php

/**
 * Enqueues styles for the Giphy Integration plugin.
 *
 * This function adds the plugin's CSS file to the front-end of the site.
 */
function giphy_integration_enqueue_styles()
{
    wp_enqueue_style(
        'giphy-integration-styles',
        plugin_dir_url(__FILE__) . '../css/giphy-integration.css',
        array(),
        '1.0',
        'all'
    );
}
add_action('wp_enqueue_scripts', 'giphy_integration_enqueue_styles');
