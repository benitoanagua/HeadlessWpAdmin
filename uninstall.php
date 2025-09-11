<?php
/**
 * Uninstall Headless WordPress Admin
 * 
 * @package HeadlessWPAdmin
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('headless_wp_settings');
delete_option('headless_wp_version');

// Clear any cached data that might be related
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
}

// Remove any custom database tables if they exist
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}headless_access_logs");