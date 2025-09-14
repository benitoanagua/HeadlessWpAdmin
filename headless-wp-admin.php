<?php

/**
 * Plugin Name: Headless WordPress Admin
 * Plugin URI: https://benitoanagua.me
 * Description: Headless administration for WordPress with modern interface and complete configuration
 * Version: 2.0.0
 * Author: Benito Anagua
 * Author URI: https://benitoanagua.me
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: headless-wp-admin
 * Domain Path: /languages
 * Requires at least: 6.0
 * Tested up to: 6.4
 * Requires PHP: 8.0
 * Network: false
 * 
 * @package HeadlessWPAdmin
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('HEADLESS_WP_ADMIN_VERSION', '2.0.0');
define('HEADLESS_WP_ADMIN_PLUGIN_FILE', __FILE__);
define('HEADLESS_WP_ADMIN_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('HEADLESS_WP_ADMIN_PLUGIN_URL', plugin_dir_url(__FILE__));
define('HEADLESS_WP_ADMIN_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Load Composer autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    // Fallback: Load core classes manually if Composer fails
    headless_wp_admin_load_core_classes();
}

/**
 * Manual loading of core classes as fallback
 */
function headless_wp_admin_load_core_classes()
{
    $core_classes = [
        'Core/Plugin.php',
        'Core/SettingsManager.php',
        'Core/HeadlessHandler.php',
        'Core/AssetManager.php',
        'Core/BlockedPageRenderer.php',
        'Core/TemplateSystem/TemplateRenderer.php',
        'Core/TemplateSystem/Component.php',
        'Core/TemplateSystem/RenderStrategy/PhpRenderStrategy.php',
        'Core/TemplateSystem/RenderStrategy/RenderStrategyInterface.php',
    ];

    foreach ($core_classes as $class) {
        $file = __DIR__ . '/src/' . $class;
        if (file_exists($file)) {
            require_once $file;
        }
    }
}

/**
 * Safe plugin initialization
 */
function headless_wp_admin_init()
{
    // Load textdomain
    load_plugin_textdomain(
        'headless-wp-admin',
        false,
        dirname(plugin_basename(HEADLESS_WP_ADMIN_PLUGIN_FILE)) . '/languages/'
    );

    // Verify that all essential classes exist
    $required_classes = [
        'HeadlessWPAdmin\Core\Plugin',
        'HeadlessWPAdmin\Core\SettingsManager',
        'HeadlessWPAdmin\Core\HeadlessHandler'
    ];

    foreach ($required_classes as $class) {
        if (!class_exists($class)) {
            error_log("Headless WP Admin: Required class {$class} not found");
            return;
        }
    }

    try {
        // Initialize the plugin directly instead of hooking to init
        $plugin = HeadlessWPAdmin\Core\Plugin::getInstance();
        $plugin->init();
    } catch (Exception $e) {
        error_log("Headless WP Admin Error: " . $e->getMessage());

        // Show error only to administrators in the dashboard
        if (is_admin() && current_user_can('manage_options')) {
            add_action('admin_notices', 'headless_wp_admin_admin_notice');
        }
    }
}

/**
 * Show admin error notice
 */
function headless_wp_admin_admin_notice()
{
    echo '<div class="notice notice-error"><p>';
    echo '<strong>Headless WP Admin Error:</strong> The plugin could not initialize correctly.';
    echo '</p></div>';
}

// Initialize the plugin on plugins_loaded with higher priority
add_action('init', 'headless_wp_admin_init', 5);

// Activation/deactivation hooks with named functions
register_activation_hook(__FILE__, 'headless_wp_admin_activate');
register_deactivation_hook(__FILE__, 'headless_wp_admin_deactivate');
register_uninstall_hook(__FILE__, 'headless_wp_admin_uninstall');

/**
 * Activation hook
 */
function headless_wp_admin_activate()
{
    if (class_exists('HeadlessWPAdmin\Core\Plugin')) {
        HeadlessWPAdmin\Core\Plugin::activate();
    }
}

/**
 * Deactivation hook
 */
function headless_wp_admin_deactivate()
{
    if (class_exists('HeadlessWPAdmin\Core\Plugin')) {
        HeadlessWPAdmin\Core\Plugin::deactivate();
    }
}

/**
 * Uninstall hook
 */
function headless_wp_admin_uninstall()
{
    if (class_exists('HeadlessWPAdmin\Core\Plugin')) {
        HeadlessWPAdmin\Core\Plugin::uninstall();
    }
}
