<?php
/**
 * Plugin Name: PHP Page Cache & Image Optimization
 * Plugin URI: https://github.com/libandreas/php-page-cache
 * Description: Page caching and image optimization for WordPress, with layout stabilization and lazy loading.
 * Version: 0.9
 * Author: Andreas Express
 * Author URI: https://ceres-assistant.com
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

define('TTFB_CLS_PLUGIN_URL', plugin_dir_url(__FILE__));

include_once __DIR__ . '/common/ttfb.php';
include_once __DIR__ . '/common/cls.php';

add_action('wp_ajax_ttfb_tools', 'render_ttfb_tools');
add_action('wp_ajax_nopriv_ttfb_tools', 'render_ttfb_tools');
?>
