<?php
namespace HeadlessWPAdmin\Core;

class Security {
    private $option_name = 'headless_wp_settings';

    public function block_theme_access() {
        $settings = get_option($this->option_name, array());
        
        if (!empty($settings['block_theme_access']) && !is_admin() && !defined('DOING_AJAX')) {
            $request = $_SERVER['REQUEST_URI'] ?? '';
            if (preg_match('/\/(wp-content\/themes\/.*\.php)/', $request)) {
                wp_die('Access denied', 'Error 403', ['response' => 403]);
            }
        }
    }

    public function disable_feeds() {
        $settings = get_option($this->option_name, array());
        
        if (!empty($settings['disable_feeds'])) {
            wp_die('Feeds disabled in headless configuration', 'Feeds Not Available', ['response' => 403]);
        }
    }

    public function disable_sitemaps($enabled) {
        $settings = get_option($this->option_name, array());
        
        if (!empty($settings['disable_sitemaps'])) {
            return false;
        }
        
        return $enabled;
    }

    public function disable_comments($open, $post_id) {
        $settings = get_option($this->option_name, array());
        
        if (!empty($settings['disable_comments'])) {
            return false;
        }
        
        return $open;
    }
}