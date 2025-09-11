<?php
namespace HeadlessWPAdmin;

class Activator {
    public static function activate() {
        // Set default options
        $default_settings = [
            'rest_api_enabled' => false,
            'rest_api_auth_required' => true,
            'rest_api_allowed_routes' => "wp/v2/posts\nwp/v2/pages\nwp/v2/media",
            'rest_api_cors_origins' => "http://localhost:3000\nhttps://yourdomain.com",
            'graphql_enabled' => true,
            'graphql_cors_enabled' => true,
            'graphql_cors_origins' => "http://localhost:3000\nhttp://localhost:3001\nhttps://studio.apollographql.com",
            'graphql_introspection' => true,
            'graphql_tracing' => false,
            'graphql_caching' => true,
            'blocked_page_enabled' => true,
            'blocked_page_title' => 'Headless Mode Active',
            'blocked_page_subtitle' => 'This site operates as a headless backend',
            'blocked_page_message' => 'The public frontend is disabled. Access the admin panel or use the GraphQL API.',
            'blocked_page_icon' => '🚀',
            'blocked_page_background_color' => '#667eea',
            'blocked_page_background_gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
            'blocked_page_show_admin_link' => true,
            'blocked_page_show_graphql_link' => true,
            'blocked_page_show_status_info' => true,
            'blocked_page_custom_css' => '',
            'blocked_page_logo_url' => '',
            'blocked_page_contact_info' => '',
            'allowed_paths' => "/wp-admin/\n/wp-login.php\n/wp-cron.php\n/graphql\n/wp-admin/admin-ajax.php",
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
        ];

        if (!get_option('headless_wp_settings')) {
            add_option('headless_wp_settings', $default_settings);
        }

        // Flush rewrite rules
        flush_rewrite_rules();
    }
}