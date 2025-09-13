<?php

/**
 * Main handler for headless mode functionality
 */

namespace HeadlessWPAdmin\Core;

use HeadlessWPAdmin\Core\Services\RESTService;
use HeadlessWPAdmin\Core\Services\GraphQLService;
use HeadlessWPAdmin\Core\Services\SecurityService;
use HeadlessWPAdmin\Core\Services\CleanupService;

class HeadlessHandler
{

    /**
     * REST service instance
     *
     * @var RESTService
     */
    private $restService;

    /**
     * GraphQL service instance
     *
     * @var GraphQLService
     */
    private $graphqlService;

    /**
     * Security service instance
     *
     * @var SecurityService
     */
    private $securityService;

    /**
     * Cleanup service instance
     *
     * @var CleanupService
     */
    private $cleanupService;

    /**
     * Blocked page renderer instance
     *
     * @var BlockedPageRenderer
     */
    private $blockedPageRenderer;

    /**
     * Settings manager instance
     *
     * @var SettingsManager
     */
    private $settingsManager;

    /**
     * Constructor
     *
     * @param SettingsManager $settingsManager
     */
    public function __construct(SettingsManager $settingsManager)
    {
        $this->settingsManager = $settingsManager;
        $this->initializeServices();
        $this->registerHooks();
    }

    /**
     * Initialize service classes
     */
    private function initializeServices(): void
    {
        $this->restService = new RESTService($this);
        $this->graphqlService = new GraphQLService($this);
        $this->securityService = new SecurityService($this);
        $this->cleanupService = new CleanupService($this);
        $this->blockedPageRenderer = new BlockedPageRenderer($this->settingsManager);
    }

    /**
     * Register WordPress hooks
     */
    private function registerHooks(): void
    {
        add_action('init', [$this, 'initHeadlessMode'], 0);
        add_action('template_redirect', [$this, 'handleFrontendRequests'], 0);
    }

    /**
     * Get plugin settings
     *
     * @return array<string, mixed>
     */
    public function get_settings(): array
    {
        return $this->settingsManager->get_settings();
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
        return $this->settingsManager->get_setting($key, $default);
    }

    /**
     * Initialize headless mode
     */
    public function initHeadlessMode(): void
    {
        if ($this->get_setting('debug_logging')) {
            error_log('Headless: Processing request: ' . ($_SERVER['REQUEST_URI'] ?? ''));
        }

        $this->restService->configure();
        $this->graphqlService->configure();
        $this->securityService->configure();
        $this->cleanupService->configure();
    }

    /**
     * Handle frontend requests
     */
    public function handleFrontendRequests(): void
    {
        if ($this->is_allowed_request()) {
            return;
        }

        if ($this->get_setting('debug_logging')) {
            error_log('Headless: Blocked request to: ' . ($_SERVER['REQUEST_URI'] ?? ''));
        }

        $this->handleBlockedRequest();
    }

    /**
     * Check if request is allowed
     *
     * @return bool
     */
    public function is_allowed_request(): bool
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';

        // Check allowed paths
        if ($this->isPathAllowed($requestUri)) {
            return true;
        }

        // Special contexts
        if ($this->isSpecialContext()) {
            return true;
        }

        // Media access
        if ($this->get_setting('media_access_enabled') && $this->isMediaRequest($requestUri)) {
            return true;
        }

        // Preview for authenticated users
        if ($this->get_setting('preview_access_enabled') && $this->isPreviewRequest()) {
            return true;
        }

        return false;
    }

    /**
     * Check if path is allowed
     *
     * @param string $requestUri
     * @return bool
     */
    private function isPathAllowed(string $requestUri): bool
    {
        $allowedPaths = array_filter(
            explode("\n", $this->get_setting('allowed_paths'))
        );

        foreach ($allowedPaths as $path) {
            $path = trim($path);
            if (!empty($path) && strpos($requestUri, $path) !== false) {
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
    private function isSpecialContext(): bool
    {
        return is_admin() ||
            (defined('DOING_AJAX') && constant('DOING_AJAX')) ||
            (defined('DOING_CRON') && constant('DOING_CRON')) ||
            (defined('WP_CLI') && constant('WP_CLI')) ||
            $this->isRestRequest() ||
            $this->isGraphqlRequest();
    }

    /**
     * Check if REST API request
     *
     * @return bool
     */
    private function isRestRequest(): bool
    {
        return defined('REST_REQUEST') ||
            strpos($_SERVER['REQUEST_URI'] ?? '', '/wp-json/') !== false;
    }

    /**
     * Check if GraphQL request
     *
     * @return bool
     */
    private function isGraphqlRequest(): bool
    {
        return strpos($_SERVER['REQUEST_URI'] ?? '', '/graphql') !== false;
    }

    /**
     * Check if media request
     *
     * @param string $uri
     * @return bool
     */
    private function isMediaRequest(string $uri): bool
    {
        return preg_match('/\/wp-content\/uploads\/.*\.(jpg|jpeg|png|gif|svg|webp|pdf|doc|docx|mp4|mp3)$/i', $uri) === 1;
    }

    /**
     * Check if preview request
     *
     * @return bool
     */
    private function isPreviewRequest(): bool
    {
        $isUserLoggedIn = is_user_logged_in();
        $canEditPosts = current_user_can('edit_posts');
        $hasPreviewParam = isset($_GET['preview']) || isset($_GET['p']) || isset($_GET['page_id']);

        return $isUserLoggedIn && $canEditPosts && $hasPreviewParam;
    }

    /**
     * Handle blocked request
     */
    private function handleBlockedRequest(): void
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';

        // Homepage redirect to admin
        if ($requestUri === '/' || $requestUri === '' || $requestUri === '/index.php') {
            wp_redirect(admin_url());
            exit;
        }

        // Show blocked page
        $this->blockedPageRenderer->render();
    }
}
