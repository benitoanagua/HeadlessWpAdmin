<?php

/**
 * Request Validator for Headless WordPress Admin
 * Centralized validation logic for headless mode requests
 */

namespace HeadlessWPAdmin\Core;

class RequestValidator
{
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
    }

    /**
     * Check if request is allowed in headless mode
     *
     * @return bool
     */
    public function isAllowedRequest(): bool
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
        if ($this->settingsManager->get_setting('media_access_enabled', true) && $this->isMediaRequest($requestUri)) {
            return true;
        }

        // Preview for authenticated users
        if ($this->settingsManager->get_setting('preview_access_enabled', true) && $this->isPreviewRequest()) {
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
    public function isPathAllowed(string $requestUri): bool
    {
        $allowedPaths = array_filter(
            explode("\n", $this->settingsManager->get_setting('allowed_paths', ''))
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
    public function isSpecialContext(): bool
    {
        return is_admin() ||
            (defined('DOING_AJAX') && DOING_AJAX) ||
            (defined('DOING_CRON') && DOING_CRON) ||
            (defined('WP_CLI') && WP_CLI) ||
            $this->isRestRequest() ||
            $this->isGraphqlRequest();
    }

    /**
     * Check if REST API request
     *
     * @return bool
     */
    public function isRestRequest(): bool
    {
        return defined('REST_REQUEST') ||
            strpos($_SERVER['REQUEST_URI'] ?? '', '/wp-json/') !== false;
    }

    /**
     * Check if GraphQL request
     *
     * @return bool
     */
    public function isGraphqlRequest(): bool
    {
        return strpos($_SERVER['REQUEST_URI'] ?? '', '/graphql') !== false;
    }

    /**
     * Check if media request
     *
     * @param string $uri
     * @return bool
     */
    public function isMediaRequest(string $uri): bool
    {
        return preg_match('/\/wp-content\/uploads\/.*\.(jpg|jpeg|png|gif|svg|webp|pdf|doc|docx|mp4|mp3)$/i', $uri) === 1;
    }

    /**
     * Check if preview request
     *
     * @return bool
     */
    public function isPreviewRequest(): bool
    {
        $isUserLoggedIn = is_user_logged_in();
        $canEditPosts = current_user_can('edit_posts');
        $hasPreviewParam = isset($_GET['preview']) || isset($_GET['p']) || isset($_GET['page_id']);

        return $isUserLoggedIn && $canEditPosts && $hasPreviewParam;
    }

    /**
     * Check if we're in blocked page context
     *
     * @return bool
     */
    public function isBlockedPageContext(): bool
    {
        return !is_admin() &&
            !wp_doing_ajax() &&
            !wp_doing_cron() &&
            !$this->isAllowedRequest();
    }
}
