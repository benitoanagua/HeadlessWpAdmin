<?php

namespace HeadlessWPAdmin\Core;

use HeadlessWPAdmin\Core\Services\RESTService;
use HeadlessWPAdmin\Core\Services\GraphQLService;
use HeadlessWPAdmin\Core\Services\SecurityService;
use HeadlessWPAdmin\Core\Services\CleanupService;
use HeadlessWPAdmin\Core\BlockedPageRenderer;

/**
 * Main handler for headless mode functionality
 */
class HeadlessHandler
{
    private RESTService $rest_service;
    private GraphQLService $graphql_service;
    private SecurityService $security_service;
    private CleanupService $cleanup_service;
    private BlockedPageRenderer $blocked_page_renderer;
    private SettingsManager $settings_manager;

    public function __construct(SettingsManager $settings_manager)
    {
        $this->settings_manager = $settings_manager;
        $this->initialize_services();
        $this->register_hooks();
    }

    /**
     * Initialize service classes
     */
    private function initialize_services(): void
    {
        $this->rest_service = new RESTService($this);
        $this->graphql_service = new GraphQLService($this);
        $this->security_service = new SecurityService($this);
        $this->cleanup_service = new CleanupService($this);
        $this->blocked_page_renderer = new BlockedPageRenderer($this->settings_manager);
    }

    /**
     * Register WordPress hooks
     */
    private function register_hooks(): void
    {
        add_action('init', [$this, 'init_headless_mode'], 0);
        add_action('template_redirect', [$this, 'handle_frontend_requests'], 0);
    }

    /**
     * Get plugin settings
     *
     * @return array<string, mixed>
     */
    public function get_settings(): array
    {
        return $this->settings_manager->get_settings();
    }

    /**
     * Get specific setting
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get_setting(string $key, $default = null)
    {
        return $this->settings_manager->get_setting($key, $default);
    }

    /**
     * Initialize headless mode
     */
    public function init_headless_mode(): void
    {
        if ($this->get_setting('debug_logging')) {
            error_log('Headless: Processing request: ' . ($_SERVER['REQUEST_URI'] ?? ''));
        }

        $this->rest_service->configure();
        $this->graphql_service->configure();
        $this->security_service->configure();
        $this->cleanup_service->configure();
    }

    /**
     * Handle frontend requests
     */
    public function handle_frontend_requests(): void
    {
        if ($this->is_allowed_request()) {
            return;
        }

        if ($this->get_setting('debug_logging')) {
            error_log('Headless: Blocked request to: ' . ($_SERVER['REQUEST_URI'] ?? ''));
        }

        $this->handle_blocked_request();
    }

    /**
     * Check if request is allowed
     *
     * @return bool
     */
    public function is_allowed_request(): bool
    {
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';

        // Check allowed paths
        if ($this->is_path_allowed($request_uri)) {
            return true;
        }

        // Special contexts
        if ($this->is_special_context()) {
            return true;
        }

        // Media access
        if ($this->get_setting('media_access_enabled') && $this->is_media_request($request_uri)) {
            return true;
        }

        // Preview for authenticated users
        if ($this->get_setting('preview_access_enabled') && $this->is_preview_request()) {
            return true;
        }

        return false;
    }

    /**
     * Check if path is allowed
     *
     * @param string $request_uri
     * @return bool
     */
    private function is_path_allowed(string $request_uri): bool
    {
        $allowed_paths = array_filter(
            explode("\n", $this->get_setting('allowed_paths'))
        );

        foreach ($allowed_paths as $path) {
            $path = trim($path);
            if (!empty($path) && strpos($request_uri, $path) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if in special context
     *
     * @return bool
     */
    private function is_special_context(): bool
    {
        return is_admin() ||
            (defined('DOING_AJAX') && DOING_AJAX) ||
            (defined('DOING_CRON') && DOING_CRON) ||
            (defined('WP_CLI') && WP_CLI) ||
            $this->is_rest_request() ||
            $this->is_graphql_request();
    }

    /**
     * Check if REST API request
     *
     * @return bool
     */
    private function is_rest_request(): bool
    {
        return defined('REST_REQUEST') ||
            strpos($_SERVER['REQUEST_URI'] ?? '', '/wp-json/') !== false;
    }

    /**
     * Check if GraphQL request
     *
     * @return bool
     */
    private function is_graphql_request(): bool
    {
        return strpos($_SERVER['REQUEST_URI'] ?? '', '/graphql') !== false;
    }

    /**
     * Check if media request
     *
     * @param string $uri
     * @return bool
     */
    private function is_media_request(string $uri): bool
    {
        return preg_match('/\/wp-content\/uploads\/.*\.(jpg|jpeg|png|gif|svg|webp|pdf|doc|docx|mp4|mp3)$/i', $uri) === 1;
    }

    /**
     * Check if preview request
     *
     * @return bool
     */
    private function is_preview_request(): bool
    {
        return is_user_logged_in() &&
            current_user_can('edit_posts') &&
            (isset($_GET['preview']) || isset($_GET['p']) || isset($_GET['page_id']));
    }

    /**
     * Handle blocked request
     */
    private function handle_blocked_request(): void
    {
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';

        // Homepage redirect to admin
        if ($request_uri === '/' || $request_uri === '' || $request_uri === '/index.php') {
            wp_redirect(admin_url());
            exit;
        }

        // Show blocked page
        $this->blocked_page_renderer->render();
    }
}
