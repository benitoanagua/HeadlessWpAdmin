<?php
namespace HeadlessWPAdmin\Core;

class Compatibility {
    public function __construct() {
        add_action('init', array($this, 'check_woocommerce'));
        add_action('init', array($this, 'check_yoast'));
        add_action('init', array($this, 'check_wp_rocket'));
        add_filter('headless_allowed_paths', array($this, 'add_plugin_paths'));
    }
    
    public function check_woocommerce() {
        if (class_exists('WooCommerce')) {
            add_filter('headless_allow_request', array($this, 'allow_woocommerce_endpoints'), 10, 2);
        }
    }
    
    public function check_yoast() {
        if (class_exists('WPSEO_Frontend')) {
            remove_action('wp_head', array('WPSEO_Frontend', 'get_instance'), 1);
        }
    }
    
    public function check_wp_rocket() {
        if (function_exists('rocket_clean_domain')) {
            add_filter('do_rocket_cache', '__return_false');
        }
    }
    
    public function allow_woocommerce_endpoints($allow, $uri) {
        $settings = get_option('headless_wp_settings', array());
        
        if (!empty($settings['woocommerce_enabled'])) {
            // Allow WooCommerce API endpoints
            if (strpos($uri, '/wp-json/wc/') !== false || 
                strpos($uri, '/wc-api/') !== false ||
                strpos($uri, '/checkout/') !== false ||
                strpos($uri, '/cart/') !== false ||
                strpos($uri, '/my-account/') !== false) {
                return true;
            }
        }
        
        return $allow;
    }
    
    public function add_plugin_paths($paths) {
        $settings = get_option('headless_wp_settings', array());
        
        // Add WooCommerce paths if enabled
        if (!empty($settings['woocommerce_enabled'])) {
            $paths[] = '/wp-json/wc/';
            $paths[] = '/wc-api/';
        }
        
        // Add other plugin paths as needed
        if (class_exists('Elementor\\Plugin')) {
            $paths[] = '/elementor/';
        }
        
        if (function_exists('wpforms')) {
            $paths[] = '/wpforms/';
        }
        
        return $paths;
    }
    
    public static function get_compatible_plugins() {
        return array(
            'woocommerce/woocommerce.php' => array(
                'name' => 'WooCommerce',
                'tested' => '7.0+',
                'settings' => array('woocommerce_enabled')
            ),
            'wordpress-seo/wp-seo.php' => array(
                'name' => 'Yoast SEO',
                'tested' => '20.0+',
                'notes' => 'Automatically disables frontend SEO features'
            ),
            'wp-rocket/wp-rocket.php' => array(
                'name' => 'WP Rocket',
                'tested' => '3.0+',
                'notes' => 'Automatically disables frontend caching'
            ),
            'elementor/elementor.php' => array(
                'name' => 'Elementor',
                'tested' => '3.0+',
                'settings' => array('elementor_enabled')
            )
        );
    }
}