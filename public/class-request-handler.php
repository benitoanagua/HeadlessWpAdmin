<?php
namespace HeadlessWPAdmin\Public;

class RequestHandler {
    private $settings;
    
    public function __construct($settings) {
        $this->settings = $settings;
    }
    
    public function handle_request() {
        if ($this->is_allowed_request()) {
            return false; // Allow the request to continue
        }
        
        if ($this->settings['debug_logging']) {
            error_log('Headless: Blocked request to: ' . ($_SERVER['REQUEST_URI'] ?? '') . ' from ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        }
        
        $this->handle_blocked_request();
        return true; // Request was handled (blocked)
    }
    
    private function is_allowed_request() {
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        
        // Allowed paths
        $allowed_paths = array_filter(explode("\n", $this->settings['allowed_paths'] ?? ''));
        
        foreach ($allowed_paths as $path) {
            $path = trim($path);
            if (!empty($path) && strpos($request_uri, $path) !== false) {
                return true;
            }
        }
        
        // Special contexts
        if (is_admin() || 
            (defined('DOING_AJAX') && DOING_AJAX) ||
            (defined('DOING_CRON') && DOING_CRON) ||
            (defined('WP_CLI') && WP_CLI)) {
            return true;
        }
        
        // Media access
        if (!empty($this->settings['media_access_enabled']) && $this->is_media_request($request_uri)) {
            return true;
        }
        
        // Preview for authenticated users
        if (!empty($this->settings['preview_access_enabled']) && is_user_logged_in() && current_user_can('edit_posts')) {
            if (isset($_GET['preview']) || isset($_GET['p']) || isset($_GET['page_id'])) {
                return true;
            }
        }
        
        // Allow through filters
        return apply_filters('headless_allow_request', false, $request_uri);
    }
    
    private function is_media_request($uri) {
        return preg_match('/\/wp-content\/uploads\/.*\.(jpg|jpeg|png|gif|svg|webp|pdf|doc|docx|mp4|mp3)$/i', $uri);
    }
    
    private function handle_blocked_request() {
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        
        // Homepage redirect
        if ($request_uri === '/' || $request_uri === '' || $request_uri === '/index.php') {
            wp_redirect(admin_url());
            exit();
        }
        
        // Show custom page
        if (!empty($this->settings['blocked_page_enabled'])) {
            $blocked_page = new BlockedPage($this->settings);
            $blocked_page->render();
        } else {
            wp_die('Access Denied - Site in headless mode', 'Access Denied', ['response' => 403]);
        }
    }
}