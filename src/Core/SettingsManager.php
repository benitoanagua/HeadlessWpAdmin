<?php

/**
 * Manages plugin settings with validation and defaults
 */

namespace HeadlessWPAdmin\Core;

class SettingsManager
{

    /**
     * Option name for settings
     */
    private const OPTION_NAME = 'headless_wp_settings';

    /**
     * Default settings configuration
     *
     * @var array<string, mixed>
     */
    private $defaultSettings = [
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
        'blocked_page_subtitle' => 'This site works as a headless backend',
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

    /**
     * Get all settings with defaults
     *
     * @return array<string, mixed>
     */
    public function get_settings(): array
    {
        $settings = get_option(self::OPTION_NAME, []);
        return wp_parse_args($settings, $this->defaultSettings);
    }

    /**
     * Get specific setting with default fallback
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get_setting(string $key, $default = null)
    {
        $settings = $this->get_settings();
        return $settings[$key] ?? $default ?? $this->defaultSettings[$key] ?? '';
    }

    /**
     * Update settings with validation
     *
     * @param array<string, mixed> $settings
     * @return bool
     */
    public function update_settings(array $settings): bool
    {
        $validatedSettings = $this->validate_settings($settings);
        return update_option(self::OPTION_NAME, $validatedSettings);
    }

    /**
     * Validate and sanitize settings
     *
     * @param array<string, mixed> $newSettings
     * @return array<string, mixed>
     */
    public function validate_settings(array $newSettings): array
    {
        $currentSettings = $this->get_settings();
        $validated = [];

        foreach ($this->defaultSettings as $key => $defaultValue) {
            if (isset($newSettings[$key])) {
                $value = $newSettings[$key];
                $validated[$key] = $this->sanitize_setting($key, $value, $defaultValue);
            } else {
                // Keep current value if no new one provided
                $validated[$key] = $currentSettings[$key] ?? $defaultValue;
            }
        }

        return $validated;
    }

    /**
     * Sanitize individual setting based on its type
     *
     * @param string $key
     * @param mixed $value
     * @param mixed $defaultValue
     * @return mixed
     */
    private function sanitize_setting(string $key, $value, $defaultValue)
    {
        // Sanitize based on field type
        if (is_bool($defaultValue)) {
            return (bool) $value;
        }

        if (filter_var($defaultValue, FILTER_VALIDATE_URL)) {
            return esc_url_raw($value);
        }

        if (in_array($key, ['blocked_page_custom_css', 'custom_redirect_rules', 'custom_headers'])) {
            return sanitize_textarea_field($value);
        }

        return sanitize_text_field($value);
    }

    /**
     * Get default settings
     *
     * @return array<string, mixed>
     */
    public function get_default_settings(): array
    {
        return $this->defaultSettings;
    }

    /**
     * Reset settings to defaults
     *
     * @return bool
     */
    public function reset_to_defaults(): bool
    {
        return update_option(self::OPTION_NAME, $this->defaultSettings);
    }

    /**
     * Delete all settings
     *
     * @return bool
     */
    public function delete_settings(): bool
    {
        return delete_option(self::OPTION_NAME);
    }
}
