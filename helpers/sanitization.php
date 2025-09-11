<?php
namespace HeadlessWPAdmin\Helpers;

class Sanitization {
    public static function sanitize_text($input) {
        return sanitize_text_field($input);
    }
    
    public static function sanitize_textarea($input) {
        return sanitize_textarea_field($input);
    }
    
    public static function sanitize_url($input) {
        return esc_url_raw($input);
    }
    
    public static function sanitize_color($input) {
        // Validate hex color
        if (preg_match('/^#([a-f0-9]{3}){1,2}$/i', $input)) {
            return $input;
        }
        
        // Validate CSS gradient
        if (strpos($input, 'gradient') !== false) {
            // Basic gradient validation
            return strip_tags($input);
        }
        
        return '#667eea'; // Default color
    }
    
    public static function sanitize_css($input) {
        // Basic CSS sanitization
        $allowed_css = array(
            'color', 'background', 'background-color', 'background-image',
            'font', 'font-family', 'font-size', 'font-weight',
            'margin', 'padding', 'border', 'border-radius',
            'width', 'height', 'max-width', 'max-height'
        );
        
        // Remove any potentially dangerous CSS
        $input = preg_replace('/<\/style.*?>/i', '', $input);
        $input = preg_replace('/<style.*?>/i', '', $input);
        $input = preg_replace('/javascript:/i', '', $input);
        $input = preg_replace('/expression\(/i', '', $input);
        
        return strip_tags($input);
    }
    
    public static function sanitize_paths($input) {
        $paths = explode("\n", $input);
        $sanitized_paths = array();
        
        foreach ($paths as $path) {
            $path = trim($path);
            if (!empty($path)) {
                $sanitized_paths[] = sanitize_text_field($path);
            }
        }
        
        return implode("\n", $sanitized_paths);
    }
    
    public static function sanitize_origins($input) {
        $origins = explode("\n", $input);
        $sanitized_origins = array();
        
        foreach ($origins as $origin) {
            $origin = trim($origin);
            if (!empty($origin)) {
                // Basic URL validation
                if (filter_var($origin, FILTER_VALIDATE_URL) || 
                    preg_match('/^https?:\/\/localhost(:\d+)?$/', $origin) ||
                    preg_match('/^https?:\/\/[a-zA-Z0-9.-]+(:\d+)?$/', $origin)) {
                    $sanitized_origins[] = esc_url_raw($origin);
                }
            }
        }
        
        return implode("\n", $sanitized_origins);
    }
    
    public static function sanitize_boolean($input) {
        return (bool) $input;
    }
    
    public static function sanitize_settings($settings) {
        $defaults = array(
            'rest_api_enabled' => false,
            'rest_api_auth_required' => true,
            'rest_api_allowed_routes' => '',
            'rest_api_cors_origins' => '',
            'graphql_enabled' => true,
            'graphql_cors_enabled' => true,
            'graphql_cors_origins' => '',
            'graphql_introspection' => true,
            'graphql_tracing' => false,
            'graphql_caching' => true,
            'blocked_page_enabled' => true,
            'blocked_page_title' => '',
            'blocked_page_subtitle' => '',
            'blocked_page_message' => '',
            'blocked_page_icon' => '',
            'blocked_page_background_color' => '',
            'blocked_page_background_gradient' => '',
            'blocked_page_show_admin_link' => true,
            'blocked_page_show_graphql_link' => true,
            'blocked_page_show_status_info' => true,
            'blocked_page_custom_css' => '',
            'blocked_page_logo_url' => '',
            'blocked_page_contact_info' => '',
            'allowed_paths' => '',
            'media_access_enabled' => true,
            'preview_access_enabled' => true,
            'security_headers_enabled' => true,
            'rate_limiting_enabled' => false,
            'debug_logging' => false,
            'block_theme_access' => true,
            'disable_feeds' => true,
            'disable_sitemaps' => true,
            'disable_comments' => true,
            'disable_embeds' => true,
            'disable_emojis' => true,
            'clean_wp_head' => true,
        );
        
        $sanitized = array();
        
        foreach ($defaults as $key => $default_value) {
            if (isset($settings[$key])) {
                $value = $settings[$key];
                
                if (is_bool($default_value)) {
                    $sanitized[$key] = self::sanitize_boolean($value);
                } elseif ($key === 'blocked_page_background_color') {
                    $sanitized[$key] = self::sanitize_color($value);
                } elseif (in_array($key, ['blocked_page_custom_css', 'blocked_page_background_gradient'])) {
                    $sanitized[$key] = self::sanitize_css($value);
                } elseif (in_array($key, ['rest_api_cors_origins', 'graphql_cors_origins'])) {
                    $sanitized[$key] = self::sanitize_origins($value);
                } elseif (in_array($key, ['allowed_paths', 'rest_api_allowed_routes'])) {
                    $sanitized[$key] = self::sanitize_paths($value);
                } elseif (filter_var($default_value, FILTER_VALIDATE_URL)) {
                    $sanitized[$key] = self::sanitize_url($value);
                } else {
                    $sanitized[$key] = self::sanitize_textarea($value);
                }
            } else {
                $sanitized[$key] = $default_value;
            }
        }
        
        return $sanitized;
    }
}