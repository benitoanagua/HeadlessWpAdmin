<?php
namespace HeadlessWPAdmin\Helpers;

class Debug {
    private static $log_file;
    
    public static function init() {
        self::$log_file = WP_CONTENT_DIR . '/debug-headless.log';
        
        if (defined('HEADLESS_DEBUG') && HEADLESS_DEBUG) {
            add_action('shutdown', array(__CLASS__, 'log_requests'));
            add_action('headless_request_blocked', array(__CLASS__, 'log_blocked_request'), 10, 2);
            add_action('headless_request_allowed', array(__CLASS__, 'log_allowed_request'), 10, 2);
        }
    }
    
    public static function log($message, $level = 'INFO') {
        $settings = get_option('headless_wp_settings', array());
        
        if (empty($settings['debug_logging']) && !defined('HEADLESS_DEBUG')) {
            return;
        }
        
        $timestamp = current_time('mysql');
        $log_entry = sprintf("[%s] %s: %s\n", $timestamp, $level, $message);
        
        // Log to file
        error_log($log_entry, 3, self::$log_file);
        
        // Also log to PHP error log if WP_DEBUG is enabled
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log($log_entry);
        }
    }
    
    public static function log_requests() {
        $request_uri = $_SERVER['REQUEST_URI'] ?? 'unknown';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        $log_message = sprintf(
            "Request: %s | IP: %s | User Agent: %s | Allowed: %s",
            $request_uri,
            $ip,
            $user_agent,
            did_action('headless_request_allowed') ? 'yes' : 'no'
        );
        
        self::log($log_message);
    }
    
    public static function log_blocked_request($uri, $ip) {
        $log_message = sprintf("BLOCKED - URI: %s | IP: %s", $uri, $ip);
        self::log($log_message, 'WARNING');
    }
    
    public static function log_allowed_request($uri, $ip) {
        $log_message = sprintf("ALLOWED - URI: %s | IP: %s", $uri, $ip);
        self::log($log_message, 'INFO');
    }
    
    public static function get_logs($lines = 100) {
        if (!file_exists(self::$log_file)) {
            return array();
        }
        
        $file = file(self::$log_file);
        $lines = min($lines, count($file));
        
        return array_slice($file, -$lines);
    }
    
    public static function clear_logs() {
        if (file_exists(self::$log_file)) {
            file_put_contents(self::$log_file, '');
        }
    }
    
    public static function get_stats() {
        if (!file_exists(self::$log_file)) {
            return array();
        }
        
        $logs = file(self::$log_file);
        $stats = array(
            'total_requests' => count($logs),
            'blocked_requests' => 0,
            'allowed_requests' => 0,
            'last_24h' => 0,
            'by_ip' => array(),
            'by_endpoint' => array()
        );
        
        $24h_ago = strtotime('-24 hours');
        
        foreach ($logs as $log) {
            if (preg_match('/BLOCKED/', $log)) {
                $stats['blocked_requests']++;
            } elseif (preg_match('/ALLOWED/', $log)) {
                $stats['allowed_requests']++;
            }
            
            // Count requests in last 24 hours
            if (preg_match('/\[([^\]]+)\]/', $log, $matches)) {
                $log_time = strtotime($matches[1]);
                if ($log_time >= $24h_ago) {
                    $stats['last_24h']++;
                }
            }
            
            // Count by IP
            if (preg_match('/IP: ([^\s|]+)/', $log, $matches)) {
                $ip = $matches[1];
                $stats['by_ip'][$ip] = ($stats['by_ip'][$ip] ?? 0) + 1;
            }
            
            // Count by endpoint
            if (preg_match('/Request: ([^\s|]+)/', $log, $matches)) {
                $endpoint = $matches[1];
                $stats['by_endpoint'][$endpoint] = ($stats['by_endpoint'][$endpoint] ?? 0) + 1;
            }
        }
        
        arsort($stats['by_ip']);
        arsort($stats['by_endpoint']);
        
        return $stats;
    }
}