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
     * Request validator instance
     *
     * @var RequestValidator
     */
    private $requestValidator;

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
        $this->requestValidator = new RequestValidator($settingsManager);
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
        return $this->requestValidator->isAllowedRequest();
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
