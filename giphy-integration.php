<?php

/**
 * Plugin Name: Giphy Integration
 * Description: A plugin to integrate with the Giphy API.
 * Version: 1.0
 * Author: Dani Matuko
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

if (!defined('GIPHY_API_KEY')) {
    exit('API key not defined.');
}

// Include class files
require_once plugin_dir_path(__FILE__) . 'includes/class-giphy-api.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-giphy-rest-endpoints.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-giphy-woocommerce.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-giphy-shortcodes.php';

// Initialize the plugin
function initialize_giphy_integration()
{
    $giphy_api              = new Giphy_API();
    $giphy_rest_endpoints   = new Giphy_REST_Endpoints($giphy_api);
    $giphy_woocommerce      = new Giphy_WooCommerce($giphy_api);
    $giphy_shortcodes       = new Giphy_Shortcodes($giphy_api);
}
add_action('init', 'initialize_giphy_integration');

/**
 * Enqueues plugin styles with high priority.
 */
function giphy_integration_enqueue_styles()
{
    wp_enqueue_style('giphy-integration-styles', plugin_dir_url(__FILE__) . 'css/giphy-integration.css', array(), '1.0', 'all');
}
add_action('wp_enqueue_scripts', 'giphy_integration_enqueue_styles');
