<?php
namespace HeadlessWPAdmin\Core;

class Cache {
    private $option_name = 'headless_wp_settings';
    
    public function __construct() {
        add_action('update_option_' . $this->option_name, array($this, 'clear_cache_on_settings_change'), 10, 2);
        add_action('headless_clear_cache', array($this, 'clear_all_caches'));
    }
    
    public function clear_cache_on_settings_change($old_value, $new_value) {
        $this->clear_graphql_cache();
        
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        do_action('headless_cache_cleared');
    }
    
    public function clear_graphql_cache() {
        if (function_exists('wpgraphql_clear_cached_schema')) {
            wpgraphql_clear_cached_schema();
        }
        
        // Clear transients that might be used for GraphQL caching
        $transients = get_transient('graphql_schema_hash') ? array('graphql_schema_hash') : array();
        
        foreach ($transients as $transient) {
            delete_transient($transient);
        }
    }
    
    public function clear_rest_api_cache() {
        if (function_exists('rest_get_server')) {
            // Clear REST API cache if any
            $server = rest_get_server();
            if (method_exists($server, 'flush_routes')) {
                $server->flush_routes();
            }
        }
    }
    
    public function clear_all_caches() {
        $this->clear_graphql_cache();
        $this->clear_rest_api_cache();
        
        // Clear object cache
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        // Clear browser cache headers
        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() - 3600) . ' GMT');
        
        do_action('headless_all_caches_cleared');
    }
    
    public static function get_cache_stats() {
        global $wpdb;
        
        $stats = array(
            'object_cache' => function_exists('wp_cache_get_stats') ? wp_cache_get_stats() : array('status' => 'Not available'),
            'transients' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE '%_transient_%'"),
            'graphql' => array(
                'schema_cached' => !empty(get_transient('graphql_schema_hash')),
                'introspection_cached' => !empty(get_transient('graphql_introspection_hash'))
            )
        );
        
        return apply_filters('headless_cache_stats', $stats);
    }
}