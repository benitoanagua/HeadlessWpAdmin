<?php
namespace HeadlessWPAdmin\Public;

class Public {
    private $plugin_name;
    private $version;
    private $option_name = 'headless_wp_settings';

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function init_headless_mode() {
        $settings = get_option($this->option_name, array());
        if (!empty($settings['debug_logging'])) {
            error_log('Headless: Processing request: ' . ($_SERVER['REQUEST_URI'] ?? ''));
        }
    }

    public function handle_frontend_requests() {
        if ($this->is_allowed_request()) {
            return;
        }

        $settings = get_option($this->option_name, array());
        if (!empty($settings['debug_logging'])) {
            error_log('Headless: Blocked request to: ' . ($_SERVER['REQUEST_URI'] ?? '') . ' from ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        }

        $this->handle_blocked_request();
    }

    private function is_allowed_request() {
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        $settings = get_option($this->option_name, array());
        
        // Allowed paths
        $allowed_paths = array_filter(explode("\n", $settings['allowed_paths'] ?? ''));
        
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
        if (!empty($settings['media_access_enabled']) && $this->is_media_request($request_uri)) {
            return true;
        }

        // Preview for authenticated users
        if (!empty($settings['preview_access_enabled']) && is_user_logged_in() && current_user_can('edit_posts')) {
            if (isset($_GET['preview']) || isset($_GET['p']) || isset($_GET['page_id'])) {
                return true;
            }
        }

        return false;
    }

    private function is_media_request($uri) {
        return preg_match('/\/wp-content\/uploads\/.*\.(jpg|jpeg|png|gif|svg|webp|pdf|doc|docx|mp4|mp3)$/i', $uri);
    }

    private function handle_blocked_request() {
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        $settings = get_option($this->option_name, array());
        
        // Homepage redirect
        if ($request_uri === '/' || $request_uri === '' || $request_uri === '/index.php') {
            wp_redirect(admin_url());
            exit();
        }

        // Show custom page
        if (!empty($settings['blocked_page_enabled'])) {
            $this->show_custom_blocked_page();
        } else {
            wp_die('Access Denied - Site in headless mode', 'Access Denied', ['response' => 403]);
        }
    }

    private function show_custom_blocked_page() {
        status_header(403);
        nocache_headers();

        $settings = get_option($this->option_name, array());
        $admin_url = admin_url();
        $graphql_url = home_url('/graphql');
        $site_name = get_bloginfo('name');

        header('Content-Type: text/html; charset=UTF-8');

        // Build dynamic styles
        $custom_styles = $this->build_custom_styles($settings);

        $html = '<!DOCTYPE html>
<html lang="' . get_locale() . '">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . esc_html($settings['blocked_page_title']) . ' - ' . esc_html($site_name) . '</title>
    <meta name="robots" content="noindex,nofollow">
    <style>' . $custom_styles . '</style>
</head>
<body>
    <div class="container">
        ' . $this->build_page_content($settings, $admin_url, $graphql_url, $site_name) . '
    </div>
</body>
</html>';

        echo $html;
        exit();
    }

    private function build_custom_styles($settings) {
        $bg_gradient = !empty($settings['blocked_page_background_gradient']) 
            ? $settings['blocked_page_background_gradient'] 
            : 'linear-gradient(135deg, ' . $settings['blocked_page_background_color'] . ' 0%, #764ba2 100%)';

        $styles = '
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: ' . $bg_gradient . ';
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2c3e50;
            line-height: 1.6;
        }
        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 700px;
            width: 90%;
            text-align: center;
            animation: slideUp 0.6s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .logo { margin-bottom: 20px; }
        .logo img { max-width: 150px; height: auto; border-radius: 10px; }
        .icon {
            font-size: 80px;
            margin-bottom: 20px;
            display: block;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #2c3e50;
            font-weight: 700;
        }
        .subtitle {
            font-size: 1.3rem;
            color: #7f8c8d;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .message {
            font-size: 1.1rem;
            color: #5a6c7d;
            margin-bottom: 30px;
            line-height: 1.7;
        }
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .status-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        .status-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .status {
            font-weight: bold;
            font-size: 1.1em;
            margin-bottom: 5px;
        }
        .status.active { color: #28a745; }
        .status.blocked { color: #dc3545; }
        .endpoint {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 25px;
            margin: 25px 0;
            position: relative;
            overflow: hidden;
        }
        .endpoint::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: ' . $bg_gradient . ';
        }
        .endpoint strong {
            display: block;
            margin-bottom: 15px;
            color: #495057;
            font-size: 1.1rem;
        }
        .endpoint code {
            background: #e9ecef;
            padding: 10px 15px;
            border-radius: 8px;
            color: #495057;
            word-break: break-all;
            font-size: 0.95rem;
            display: block;
            font-family: "SF Mono", Monaco, monospace;
        }
        .buttons {
            margin-top: 30px;
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 25px;
            background: ' . $bg_gradient . ';
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            font-size: 1rem;
        }
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .contact-info {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 15px;
            border-left: 4px solid #007cba;
        }
        .footer {
            margin-top: 30px;
            font-size: 0.9rem;
            color: #6c757d;
        }
        @media (max-width: 768px) {
            .container { padding: 30px 20px; }
            h1 { font-size: 2rem; }
            .buttons { flex-direction: column; align-items: center; }
            .btn { width: 100%; max-width: 280px; justify-content: center; }
            .status-grid { grid-template-columns: 1fr; }
        }
        ' . ($settings['blocked_page_custom_css'] ?? '');

        return $styles;
    }

    private function build_page_content($settings, $admin_url, $graphql_url, $site_name) {
        $content = '';

        // Logo
        if (!empty($settings['blocked_page_logo_url'])) {
            $content .= '<div class="logo"><img src="' . esc_url($settings['blocked_page_logo_url']) . '" alt="Logo"></div>';
        }

        // Icon
        if (!empty($settings['blocked_page_icon'])) {
            $content .= '<div class="icon">' . esc_html($settings['blocked_page_icon']) . '</div>';
        }

        // Title and subtitle
        $content .= '<h1>' . esc_html($settings['blocked_page_title']) . '</h1>';
        if (!empty($settings['blocked_page_subtitle'])) {
            $content .= '<p class="subtitle">' . esc_html($settings['blocked_page_subtitle']) . '</p>';
        }

        // Message
        if (!empty($settings['blocked_page_message'])) {
            $content .= '<div class="message">' . wp_kses_post(wpautop($settings['blocked_page_message'])) . '</div>';
        }

        // Status info
        if (!empty($settings['blocked_page_show_status_info'])) {
            $content .= '<div class="status-grid">';
            
            if (!empty($settings['graphql_enabled'])) {
                $content .= '<div class="status-item">
                    <div class="status active">✅ GraphQL API</div>
                    <small>Available for queries</small>
                </div>';
            }
            
            if (!empty($settings['rest_api_enabled'])) {
                $content .= '<div class="status-item">
                    <div class="status active">✅ REST API</div>
                    <small>Available with configuration</small>
                </div>';
            }
            
            $content .= '<div class="status-item">
                <div class="status active">✅ Administration</div>
                <small>Control panel active</small>
            </div>';
            
            $content .= '<div class="status-item">
                <div class="status blocked">❌ Frontend</div>
                <small>Public access disabled</small>
            </div>';
            
            $content .= '</div>';
        }

        // GraphQL endpoint
        if (!empty($settings['blocked_page_show_graphql_link']) && !empty($settings['graphql_enabled'])) {
            $content .= '<div class="endpoint">
                <strong>🔌 GraphQL Endpoint</strong>
                <code>' . esc_url($graphql_url) . '</code>
            </div>';
        }

        // Buttons
        $content .= '<div class="buttons">';
        if (!empty($settings['blocked_page_show_admin_link'])) {
            $content .= '<a href="' . esc_url($admin_url) . '" class="btn">
                <span>⚙️</span> Administration
            </a>';
        }
        if (!empty($settings['blocked_page_show_graphql_link']) && !empty($settings['graphql_enabled'])) {
            $content .= '<a href="' . esc_url($graphql_url) . '" class="btn" target="_blank">
                <span>⚡</span> GraphQL
            </a>';
        }
        $content .= '</div>';

        // Contact information
        if (!empty($settings['blocked_page_contact_info'])) {
            $content .= '<div class="contact-info">' . wp_kses_post(wpautop($settings['blocked_page_contact_info'])) . '</div>';
        }

        // Footer
        $content .= '<div class="footer">
            <p><strong>' . esc_html($site_name) . '</strong> - Headless CMS</p>
            <small>Develop your frontend using available APIs</small>
        </div>';

        return $content;
    }

    public function add_security_headers() {
        $settings = get_option($this->option_name, array());
        
        if (!empty($settings['security_headers_enabled']) && !is_admin()) {
            header('X-Content-Type-Options: nosniff');
            header('X-Frame-Options: SAMEORIGIN');
            header('X-XSS-Protection: 1; mode=block');
            header('Referrer-Policy: strict-origin-when-cross-origin');
        }
    }
}