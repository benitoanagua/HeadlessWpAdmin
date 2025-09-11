<?php
namespace HeadlessWPAdmin\Helpers;

class Validation {
    public static function is_valid_url($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    public static function is_valid_hex_color($color) {
        return preg_match('/^#([a-f0-9]{3}){1,2}$/i', $color);
    }
    
    public static function is_valid_cors_origin($origin) {
        if (empty($origin)) return false;
        
        return self::is_valid_url($origin) || 
               preg_match('/^https?:\/\/localhost(:\d+)?$/', $origin) ||
               preg_match('/^https?:\/\/[a-zA-Z0-9.-]+(:\d+)?$/', $origin);
    }
    
    public static function is_valid_path($path) {
        if (empty($path)) return false;
        
        // Basic path validation
        return preg_match('/^[a-zA-Z0-9_\-\.\/\*]+$/', $path);
    }
    
    public static function is_valid_graphql_endpoint() {
        // Check if GraphQL endpoint is accessible
        $response = wp_remote_get(home_url('/graphql'), array(
            'timeout' => 5,
            'headers' => array('Accept' => 'application/json')
        ));
        
        return !is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200;
    }
    
    public static function is_valid_rest_endpoint() {
        // Check if REST API endpoint is accessible
        $response = wp_remote_get(home_url('/wp-json/'), array(
            'timeout' => 5,
            'headers' => array('Accept' => 'application/json')
        ));
        
        return !is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200;
    }
    
    public static function validate_settings($settings) {
        $errors = array();
        
        // Validate URLs
        if (!empty($settings['blocked_page_logo_url']) && !self::is_valid_url($settings['blocked_page_logo_url'])) {
            $errors['blocked_page_logo_url'] = 'Invalid URL format';
        }
        
        // Validate colors
        if (!empty($settings['blocked_page_background_color']) && !self::is_valid_hex_color($settings['blocked_page_background_color'])) {
            $errors['blocked_page_background_color'] = 'Invalid color format. Use hex format (#RRGGBB)';
        }
        
        // Validate CORS origins
        $cors_fields = ['rest_api_cors_origins', 'graphql_cors_origins'];
        foreach ($cors_fields as $field) {
            if (!empty($settings[$field])) {
                $origins = explode("\n", $settings[$field]);
                foreach ($origins as $origin) {
                    $origin = trim($origin);
                    if (!empty($origin) && !self::is_valid_cors_origin($origin)) {
                        $errors[$field] = 'Invalid CORS origin: ' . $origin;
                        break;
                    }
                }
            }
        }
        
        // Validate paths
        $path_fields = ['allowed_paths', 'rest_api_allowed_routes'];
        foreach ($path_fields as $field) {
            if (!empty($settings[$field])) {
                $paths = explode("\n", $settings[$field]);
                foreach ($paths as $path) {
                    $path = trim($path);
                    if (!empty($path) && !self::is_valid_path($path)) {
                        $errors[$field] = 'Invalid path format: ' . $path;
                        break;
                    }
                }
            }
        }
        
        return $errors;
    }
    
    public static function is_wpgraphql_installed() {
        return class_exists('WPGraphQL');
    }
    
    public static function is_plugin_active($plugin) {
        if (!function_exists('is_plugin_active')) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        return is_plugin_active($plugin);
    }
}