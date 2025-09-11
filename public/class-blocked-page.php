<?php
namespace HeadlessWPAdmin\Public;

class BlockedPage {
    private $settings;
    
    public function __construct($settings) {
        $this->settings = $settings;
    }
    
    public function render() {
        status_header(403);
        nocache_headers();
        
        $admin_url = admin_url();
        $graphql_url = home_url('/graphql');
        $site_name = get_bloginfo('name');
        
        header('Content-Type: text/html; charset=UTF-8');
        
        // Use template if available, otherwise use default
        $template_path = apply_filters('headless_blocked_page_template', 
            HEADLESS_WP_ADMIN_PLUGIN_PATH . 'templates/blocked-page.php');
        
        if (file_exists($template_path)) {
            include $template_path;
        } else {
            $this->render_default();
        }
        
        exit;
    }
    
    private function render_default() {
        $custom_styles = $this->build_custom_styles();
        $page_content = $this->build_page_content();
        
        echo '<!DOCTYPE html>
<html lang="' . get_locale() . '">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . esc_html($this->settings['blocked_page_title']) . ' - ' . esc_html(get_bloginfo('name')) . '</title>
    <meta name="robots" content="noindex,nofollow">
    <style>' . $custom_styles . '</style>
</head>
<body>
    <div class="container">
        ' . $page_content . '
    </div>
</body>
</html>';
    }
    
    private function build_custom_styles() {
        $bg_gradient = !empty($this->settings['blocked_page_background_gradient']) 
            ? $this->settings['blocked_page_background_gradient'] 
            : 'linear-gradient(135deg, ' . $this->settings['blocked_page_background_color'] . ' 0%, #764ba2 100%)';
        
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
        ' . ($this->settings['blocked_page_custom_css'] ?? '');
        
        return $styles;
    }
    
    private function build_page_content() {
        $admin_url = admin_url();
        $graphql_url = home_url('/graphql');
        $site_name = get_bloginfo('name');
        
        $content = '';
        
        // Logo
        if (!empty($this->settings['blocked_page_logo_url'])) {
            $content .= '<div class="logo"><img src="' . esc_url($this->settings['blocked_page_logo_url']) . '" alt="Logo"></div>';
        }
        
        // Icon
        if (!empty($this->settings['blocked_page_icon'])) {
            $content .= '<div class="icon">' . esc_html($this->settings['blocked_page_icon']) . '</div>';
        }
        
        // Title and subtitle
        $content .= '<h1>' . esc_html($this->settings['blocked_page_title']) . '</h1>';
        if (!empty($this->settings['blocked_page_subtitle'])) {
            $content .= '<p class="subtitle">' . esc_html($this->settings['blocked_page_subtitle']) . '</p>';
        }
        
        // Message
        if (!empty($this->settings['blocked_page_message'])) {
            $content .= '<div class="message">' . wp_kses_post(wpautop($this->settings['blocked_page_message'])) . '</div>';
        }
        
        // Status info
        if (!empty($this->settings['blocked_page_show_status_info'])) {
            $content .= $this->build_status_info();
        }
        
        // GraphQL endpoint
        if (!empty($this->settings['blocked_page_show_graphql_link']) && !empty($this->settings['graphql_enabled'])) {
            $content .= '<div class="endpoint">
                <strong>🔌 GraphQL Endpoint</strong>
                <code>' . esc_url($graphql_url) . '</code>
            </div>';
        }
        
        // Buttons
        $content .= '<div class="buttons">';
        if (!empty($this->settings['blocked_page_show_admin_link'])) {
            $content .= '<a href="' . esc_url($admin_url) . '" class="btn">
                <span>⚙️</span> Administration
            </a>';
        }
        if (!empty($this->settings['blocked_page_show_graphql_link']) && !empty($this->settings['graphql_enabled'])) {
            $content .= '<a href="' . esc_url($graphql_url) . '" class="btn" target="_blank">
                <span>⚡</span> GraphQL
            </a>';
        }
        $content .= '</div>';
        
        // Contact information
        if (!empty($this->settings['blocked_page_contact_info'])) {
            $content .= '<div class="contact-info">' . wp_kses_post(wpautop($this->settings['blocked_page_contact_info'])) . '</div>';
        }
        
        // Footer
        $content .= '<div class="footer">
            <p><strong>' . esc_html($site_name) . '</strong> - Headless CMS</p>
            <small>Develop your frontend using available APIs</small>
        </div>';
        
        return $content;
    }
    
    private function build_status_info() {
        return '<div class="status-grid">
            ' . ($this->settings['graphql_enabled'] ? '
            <div class="status-item">
                <div class="status active">✅ GraphQL API</div>
                <small>Available for queries</small>
            </div>' : '') . '
            
            ' . ($this->settings['rest_api_enabled'] ? '
            <div class="status-item">
                <div class="status active">✅ REST API</div>
                <small>Available with configuration</small>
            </div>' : '') . '
            
            <div class="status-item">
                <div class="status active">✅ Administration</div>
                <small>Control panel active</small>
            </div>
            
            <div class="status-item">
                <div class="status blocked">❌ Frontend</div>
                <small>Public access disabled</small>
            </div>
        </div>';
    }
}