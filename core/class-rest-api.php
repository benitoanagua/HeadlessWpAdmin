<?php
namespace HeadlessWPAdmin\Core;

class RestAPI {
    private $option_name = 'headless_wp_settings';

    public function setup_cors() {
        $settings = get_option($this->option_name, array());
        
        if (empty($settings['rest_api_enabled'])) {
            add_filter('rest_enabled', '__return_false');
            add_filter('rest_jsonp_enabled', '__return_false');
            return;
        }

        $origins = array_filter(explode("\n", $settings['rest_api_cors_origins'] ?? ''));
        
        remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
        add_filter('rest_pre_serve_request', function ($value) use ($origins) {
            $origin = get_http_origin();
            if ($origin && in_array($origin, $origins)) {
                header('Access-Control-Allow-Origin: ' . esc_url_raw($origin));
                header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
                header('Access-Control-Allow-Credentials: true');
                header('Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce');
            }
            return $value;
        });
    }

    public function require_authentication($result) {
        $settings = get_option($this->option_name, array());
        
        if (empty($settings['rest_api_enabled'])) {
            if (is_admin() || current_user_can('manage_options')) {
                return $result;
            }
            return new \WP_Error('rest_disabled', 'REST API disabled in headless configuration', ['status' => 403]);
        }

        if (!empty($settings['rest_api_auth_required']) && !empty($result)) {
            return $result;
        }
        
        if (!empty($settings['rest_api_auth_required']) && !is_user_logged_in() && !current_user_can('read')) {
            return new \WP_Error('rest_forbidden', 'Authentication required', ['status' => 401]);
        }
        
        return $result;
    }

    public function filter_routes($result, $request, $route) {
        $settings = get_option($this->option_name, array());
        
        if (empty($settings['rest_api_enabled']) || empty($settings['rest_api_allowed_routes'])) {
            return $result;
        }

        $allowed_routes = array_filter(explode("\n", $settings['rest_api_allowed_routes']));
        $allowed = false;
        
        foreach ($allowed_routes as $allowed_route) {
            if (strpos($route, trim($allowed_route)) !== false) {
                $allowed = true;
                break;
            }
        }
        
        if (!$allowed) {
            return new \WP_Error('rest_route_forbidden', 'Route not allowed in headless configuration', ['status' => 403]);
        }
        
        return $result;
    }
}