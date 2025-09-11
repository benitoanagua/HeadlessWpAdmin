<?php
namespace HeadlessWPAdmin\Helpers;

function is_headless_mode_active() {
    $settings = get_option('headless_wp_settings', []);
    return !empty($settings);
}

function get_headless_setting($key, $default = null) {
    $settings = get_option('headless_wp_settings', []);
    return isset($settings[$key]) ? $settings[$key] : $default;
}

function is_headless_graphql_enabled() {
    return get_headless_setting('graphql_enabled', true);
}

function is_headless_rest_enabled() {
    return get_headless_setting('rest_api_enabled', false);
}

function headless_debug_info() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $settings = get_option('headless_wp_settings', []);
    $info = [
        'settings' => $settings,
        'current_request' => $_SERVER['REQUEST_URI'] ?? '',
        'is_admin' => is_admin(),
        'is_ajax' => defined('DOING_AJAX') && DOING_AJAX,
        'user_can_manage' => current_user_can('manage_options'),
        'graphql_active' => class_exists('WPGraphQL'),
        'wp_version' => get_bloginfo('version'),
        'php_version' => PHP_VERSION,
    ];

    return $info;
}