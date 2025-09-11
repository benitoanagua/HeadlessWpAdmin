<?php
/**
 * Plugin Name: Headless WordPress Admin
 * Description: Complete headless WordPress configuration with admin dashboard controls
 * Version: 2.0
 * Author: Your Name
 * License: GPL v2 or later
 * Text Domain: headless-wp-admin
 */

// If this file is called directly, abort
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('HEADLESS_WP_ADMIN_VERSION', '2.0');
define('HEADLESS_WP_ADMIN_PLUGIN_URL', plugin_dir_url(__FILE__));
define('HEADLESS_WP_ADMIN_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('HEADLESS_WP_ADMIN_BASENAME', plugin_basename(__FILE__));

// Autoloader for plugin classes
spl_autoload_register(function ($class_name) {
    if (strpos($class_name, 'HeadlessWPAdmin\\') === 0) {
        $class_name = str_replace('HeadlessWPAdmin\\', '', $class_name);
        $class_name = str_replace('\\', '/', $class_name);
        $file_path = HEADLESS_WP_ADMIN_PLUGIN_PATH . 'includes/class-' . strtolower($class_name) . '.php';
        
        if (file_exists($file_path)) {
            require_once $file_path;
        }
    }
});

// Initialize the plugin
function headless_wp_admin_init() {
    $plugin = new HeadlessWPAdmin\HeadlessWPAdmin();
    $plugin->run();
}

add_action('plugins_loaded', 'headless_wp_admin_init');

// Activation hook
register_activation_hook(__FILE__, array('HeadlessWPAdmin\Activator', 'activate'));

// Deactivation hook
register_deactivation_hook(__FILE__, array('HeadlessWPAdmin\Deactivator', 'deactivate'));

// Uninstall hook
register_uninstall_hook(__FILE__, array('HeadlessWPAdmin\Uninstaller', 'uninstall'));