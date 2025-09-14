<?php

/**
 * Asset Manager for Headless WordPress Admin
 * Centralized management of CSS and JS assets
 */

namespace HeadlessWPAdmin\Core;

class AssetManager
{

    /**
     * Settings manager instance
     *
     * @var SettingsManager
     */
    private $settingsManager;

    /**
     * Plugin version
     *
     * @var string
     */
    private $version;

    /**
     * Plugin URL
     *
     * @var string
     */
    private $pluginUrl;

    /**
     * Plugin path
     *
     * @var string
     */
    private $pluginPath;

    /**
     * Constructor
     *
     * @param SettingsManager $settingsManager
     * @param string $version
     * @param string $pluginUrl
     * @param string $pluginPath
     */
    public function __construct(SettingsManager $settingsManager, string $version, string $pluginUrl, string $pluginPath)
    {
        $this->settingsManager = $settingsManager;
        $this->version = $version;
        $this->pluginUrl = $pluginUrl;
        $this->pluginPath = $pluginPath;

        $this->init();
    }

    /**
     * Initialize asset management
     */
    private function init(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueFrontendAssets']);
    }

    /**
     * Enqueue admin assets
     *
     * @param string $hook
     */
    public function enqueueAdminAssets(string $hook): void
    {
        // Only load on plugin admin pages
        if (!$this->isPluginAdminPage($hook)) {
            return;
        }

        // Main admin style (compiled by Vite)
        wp_enqueue_style(
            'headless-admin-main',
            $this->pluginUrl . 'public/assets/style.css',
            [],
            $this->version
        );

        // Main admin script (compiled by Vite)
        wp_enqueue_script(
            'headless-admin-main',
            $this->pluginUrl . 'public/assets/main.js',
            ['jquery', 'wp-color-picker'],
            $this->version,
            true
        );

        // Localize script with configuration
        wp_localize_script('headless-admin-main', 'headlessAdmin', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('headless_admin_nonce'),
            'homeUrl' => home_url(),
            'graphqlUrl' => home_url('/graphql'),
            'restUrl' => home_url('/wp-json/'),
            'strings' => [
                'confirmReset' => __('Reset all settings?', 'headless-wp-admin'),
                'graphqlTestSuccess' => __('GraphQL is responding correctly', 'headless-wp-admin'),
                'graphqlTestError' => __('Error connecting to GraphQL', 'headless-wp-admin'),
            ]
        ]);

        // Required WordPress assets
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueueFrontendAssets(): void
    {
        // Only load on blocked pages
        if (!$this->isBlockedPageContext()) {
            return;
        }

        // Blocked page style (compiled by Vite)
        wp_enqueue_style(
            'headless-blocked-page',
            $this->pluginUrl . 'public/assets/style.css',
            [],
            $this->version
        );

        // Custom inline CSS for blocked page
        $customCss = $this->generateBlockedPageCustomCss();
        if (!empty($customCss)) {
            wp_add_inline_style('headless-blocked-page', $customCss);
        }
    }

    /**
     * Generate custom CSS for blocked page
     *
     * @return string
     */
    private function generateBlockedPageCustomCss(): string
    {
        $customCss = $this->settingsManager->get_setting('blocked_page_custom_css', '');

        if (!empty($customCss)) {
            // Sanitize CSS
            $customCss = wp_strip_all_tags($customCss);
            $customCss = preg_replace('/[\\0-\\x1F\\x7F]/', '', $customCss);
            return '/* Custom CSS */ ' . $customCss;
        }

        return '';
    }

    /**
     * Check if we're on plugin admin page
     *
     * @param string $hook
     * @return bool
     */
    private function isPluginAdminPage(string $hook = ''): bool
    {
        global $pagenow;

        $currentHook = $hook ?: ($pagenow ?? '');
        $page = $_GET['page'] ?? '';

        // Corrected logic
        $isHeadlessHook = strpos($currentHook, 'headless-') === 0;
        $isHeadlessPage = strpos($page, 'headless-') === 0;
        $isAdminPageWithHeadless = $currentHook === 'admin.php' && strpos($page, 'headless-') === 0;

        return $isHeadlessHook || $isHeadlessPage || $isAdminPageWithHeadless;
    }

    /**
     * Check if we're in blocked page context
     *
     * @return bool
     */
    private function isBlockedPageContext(): bool
    {
        return !is_admin() &&
            !wp_doing_ajax() &&
            !wp_doing_cron() &&
            !$this->isHeadlessRequestAllowed();
    }

    /**
     * Check if headless request is allowed (using settings directly)
     *
     * @return bool
     */
    private function isHeadlessRequestAllowed(): bool
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';

        // Check allowed paths
        $allowedPaths = array_filter(
            explode("\n", $this->settingsManager->get_setting('allowed_paths', ''))
        );

        foreach ($allowedPaths as $path) {
            $path = trim($path);
            if (!empty($path) && strpos($requestUri, $path) !== false) {
                return true;
            }
        }

        // Special contexts
        if (
            is_admin() ||
            (defined('DOING_AJAX') && constant('DOING_AJAX')) ||
            (defined('DOING_CRON') && constant('DOING_CRON')) ||
            (defined('WP_CLI') && constant('WP_CLI')) ||
            strpos($requestUri, '/wp-json/') !== false ||
            strpos($requestUri, '/graphql') !== false
        ) {
            return true;
        }

        // Media access
        if (
            $this->settingsManager->get_setting('media_access_enabled', true) &&
            preg_match('/\/wp-content\/uploads\/.*\.(jpg|jpeg|png|gif|svg|webp|pdf|doc|docx|mp4|mp3)$/i', $requestUri) === 1
        ) {
            return true;
        }

        // Preview for authenticated users
        if (
            $this->settingsManager->get_setting('preview_access_enabled', true) &&
            is_user_logged_in() &&
            current_user_can('edit_posts') &&
            (isset($_GET['preview']) || isset($_GET['p']) || isset($_GET['page_id']))
        ) {
            return true;
        }

        return false;
    }

    /**
     * Get CSS for blocked page (for template usage)
     *
     * @return string
     */
    public function getBlockedPageCss(): string
    {
        $cssFile = $this->pluginPath . 'public/assets/style.css';
        $baseCss = file_exists($cssFile) ? file_get_contents($cssFile) : '';

        $customCss = $this->generateBlockedPageCustomCss();

        return $baseCss . $customCss;
    }
}
