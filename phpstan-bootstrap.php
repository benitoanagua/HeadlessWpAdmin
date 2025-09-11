<?php
// Bootstrap file for PHPStan
// Define WordPress constants and functions for static analysis

if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/wordpress/');
}

// Definir constantes comunes de WordPress
$wp_constants = [
    'WP_DEBUG',
    'WP_CONTENT_DIR',
    'WP_CONTENT_URL',
    'WP_PLUGIN_DIR',
    'WP_PLUGIN_URL',
    'WPLANG',
    'WPINC',
    'TEMPLATEPATH',
    'STYLESHEETPATH',
    'DB_HOST',
    'DB_NAME',
    'DB_USER',
    'DB_PASSWORD',
    'DB_CHARSET',
    'DB_COLLATE',
    'AUTH_KEY',
    'SECURE_AUTH_KEY',
    'LOGGED_IN_KEY',
    'NONCE_KEY',
    'AUTH_SALT',
    'SECURE_AUTH_SALT',
    'LOGGED_IN_SALT',
    'NONCE_SALT'
];

foreach ($wp_constants as $constant) {
    if (!defined($constant)) {
        define($constant, '');
    }
}

// Definir algunas funciones básicas de WordPress si no existen
if (!function_exists('plugin_dir_path')) {
    function plugin_dir_path($file)
    {
        return dirname($file) . '/';
    }
}

if (!function_exists('plugin_dir_url')) {
    function plugin_dir_url($file)
    {
        return 'http://example.com/wp-content/plugins/' . basename(dirname($file)) . '/';
    }
}

if (!function_exists('plugin_basename')) {
    function plugin_basename($file)
    {
        return basename(dirname($file)) . '/' . basename($file);
    }
}

if (!function_exists('load_plugin_textdomain')) {
    function load_plugin_textdomain($domain, $deprecated = false, $rel_path = false)
    {
        return true;
    }
}

if (!function_exists('add_action')) {
    function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1)
    {
        return true;
    }
}

if (!function_exists('add_filter')) {
    function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1)
    {
        return true;
    }
}

if (!function_exists('is_admin')) {
    function is_admin()
    {
        return false;
    }
}

if (!function_exists('wp_enqueue_script')) {
    function wp_enqueue_script($handle, $src = '', $deps = [], $ver = false, $in_footer = false)
    {
        return true;
    }
}

if (!function_exists('wp_enqueue_style')) {
    function wp_enqueue_style($handle, $src = '', $deps = [], $ver = false, $media = 'all')
    {
        return true;
    }
}

if (!function_exists('flush_rewrite_rules')) {
    function flush_rewrite_rules($hard = true)
    {
        return true;
    }
}

if (!function_exists('register_activation_hook')) {
    function register_activation_hook($file, $function)
    {
        return true;
    }
}

if (!function_exists('register_deactivation_hook')) {
    function register_deactivation_hook($file, $function)
    {
        return true;
    }
}

if (!function_exists('register_uninstall_hook')) {
    function register_uninstall_hook($file, $function)
    {
        return true;
    }
}
