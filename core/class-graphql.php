<?php
namespace HeadlessWPAdmin\Core;

class GraphQL {
    private $option_name = 'headless_wp_settings';

    public function allowed_origins($origins) {
        $settings = get_option($this->option_name, array());
        
        if (empty($settings['graphql_enabled']) || empty($settings['graphql_cors_enabled'])) {
            return $origins;
        }

        $custom_origins = array_filter(explode("\n", $settings['graphql_cors_origins'] ?? ''));
        return array_merge($origins, $custom_origins);
    }

    public function allow_credentials() {
        $settings = get_option($this->option_name, array());
        return !empty($settings['graphql_enabled']) && !empty($settings['graphql_cors_enabled']);
    }

    public function allowed_headers($headers) {
        $settings = get_option($this->option_name, array());
        
        if (empty($settings['graphql_enabled']) || empty($settings['graphql_cors_enabled'])) {
            return $headers;
        }

        return array_merge($headers, [
            'Authorization', 'Content-Type', 'X-Requested-With', 'X-WP-Nonce',
            'Cache-Control', 'Accept-Language', 'Apollo-Require-Preflight'
        ]);
    }

    public function enable_introspection() {
        $settings = get_option($this->option_name, array());
        return !empty($settings['graphql_enabled']) && !empty($settings['graphql_introspection']);
    }

    public function enable_tracing() {
        $settings = get_option($this->option_name, array());
        return !empty($settings['graphql_enabled']) && !empty($settings['graphql_tracing']);
    }

    public function enable_caching() {
        $settings = get_option($this->option_name, array());
        return !empty($settings['graphql_enabled']) && !empty($settings['graphql_caching']);
    }
}