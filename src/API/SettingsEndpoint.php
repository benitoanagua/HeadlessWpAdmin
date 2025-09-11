<?php

namespace HeadlessWPAdmin\API;

use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

/**
 * Clase para manejar el endpoint REST de configuración
 */
class SettingsEndpoint
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes(): void
    {
        register_rest_route('headless/v1', '/config', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_config'],
                'permission_callback' => [$this, 'check_permissions'],
            ],
            [
                'methods' => 'POST',
                'callback' => [$this, 'update_config'],
                'permission_callback' => [$this, 'check_permissions'],
            ]
        ]);
    }

    public function get_config(): WP_REST_Response
    {
        $settings = get_option('headless_wp_settings', []);

        return rest_ensure_response([
            'success' => true,
            'data' => $settings,
            'endpoints' => [
                'graphql' => home_url('/graphql'),
                'rest' => home_url('/wp-json/'),
                'admin' => admin_url(),
            ]
        ]);
    }

    /**
     * @param WP_REST_Request<array<string, mixed>> $request
     */
    public function update_config(WP_REST_Request $request): WP_REST_Response
    {
        $new_settings = $request->get_json_params();
        $current_settings = get_option('headless_wp_settings', []);

        // Validar y sanitizar los settings
        $validated_settings = $this->validate_settings($new_settings, $current_settings);

        update_option('headless_wp_settings', $validated_settings);

        return rest_ensure_response([
            'success' => true,
            'message' => 'Configuración actualizada correctamente'
        ]);
    }

    /**
     * @param array<string, mixed> $new_settings
     * @param array<string, mixed> $current_settings
     * @return array<string, mixed>
     */
    private function validate_settings(array $new_settings, array $current_settings): array
    {
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
            'blocked_page_title' => 'Headless Mode Activo',
            'blocked_page_subtitle' => 'Este sitio funciona como backend headless',
            'blocked_page_message' => 'El frontend público está deshabilitado. Accede al panel de administración o utiliza la API GraphQL.',
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

        $validated = [];

        foreach ($default_settings as $key => $default_value) {
            if (isset($new_settings[$key])) {
                $value = $new_settings[$key];

                // Sanitizar según el tipo de campo
                if (is_bool($default_value)) {
                    $validated[$key] = (bool) $value;
                } elseif (filter_var($default_value, FILTER_VALIDATE_URL)) {
                    $validated[$key] = esc_url_raw($value);
                } elseif (in_array($key, ['blocked_page_custom_css', 'custom_redirect_rules', 'custom_headers'])) {
                    $validated[$key] = sanitize_textarea_field($value);
                } else {
                    $validated[$key] = sanitize_text_field($value);
                }
            } else {
                // Mantener el valor actual si no se proporciona uno nuevo
                $validated[$key] = $current_settings[$key] ?? $default_value;
            }
        }

        return $validated;
    }

    public function check_permissions(): bool
    {
        return current_user_can('manage_options');
    }
}
