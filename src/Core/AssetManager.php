<?php

/**
 * Asset Manager for Headless WordPress Admin
 * Centralized management of CSS and JS assets
 */

namespace HeadlessWPAdmin\Core;

class AssetManager
{

    /**
     * Headless handler instance
     *
     * @var HeadlessHandler
     */
    private $headlessHandler;

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
     * @param HeadlessHandler $headlessHandler
     * @param string $version
     * @param string $pluginUrl
     * @param string $pluginPath
     */
    public function __construct(HeadlessHandler $headlessHandler, string $version, string $pluginUrl, string $pluginPath)
    {
        $this->headlessHandler = $headlessHandler;
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
        add_action('admin_head', [$this, 'addAdminDynamicStyles']);
        add_action('wp_head', [$this, 'addFrontendDynamicStyles'], 5);
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

        // Admin CSS
        $this->enqueueAdminStyles();

        // Admin JS
        $this->enqueueAdminScripts();

        // Required WordPress assets
        $this->enqueueWordPressAssets();
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

        $this->enqueueBlockedPageStyles();
    }

    /**
     * Enqueue admin styles
     */
    private function enqueueAdminStyles(): void
    {
        // Main admin style
        wp_enqueue_style(
            'headless-admin-main',
            $this->pluginUrl . 'assets/css/admin/main.css',
            [],
            $this->version
        );

        // Admin components
        wp_enqueue_style(
            'headless-admin-components',
            $this->pluginUrl . 'assets/css/admin/components.css',
            ['headless-admin-main'],
            $this->version
        );

        // Forms
        wp_enqueue_style(
            'headless-admin-forms',
            $this->pluginUrl . 'assets/css/admin/forms.css',
            ['headless-admin-main'],
            $this->version
        );
    }

    /**
     * Enqueue admin scripts
     */
    private function enqueueAdminScripts(): void
    {
        wp_enqueue_script(
            'headless-admin-main',
            $this->pluginUrl . 'assets/js/admin/main.js',
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
    }

    /**
     * Enqueue WordPress assets
     */
    private function enqueueWordPressAssets(): void
    {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }

    /**
     * Enqueue blocked page styles
     */
    private function enqueueBlockedPageStyles(): void
    {
        $settings = $this->headlessHandler->get_settings();
        $cssHash = $this->generateCssHash($settings);

        // Base blocked page style
        wp_enqueue_style(
            'headless-blocked-page',
            $this->pluginUrl . 'assets/css/frontend/blocked-page.css',
            [],
            $this->version . '-' . $cssHash
        );

        // Custom inline CSS
        $customCss = $this->generateBlockedPageCustomCss($settings);
        if (!empty($customCss)) {
            wp_add_inline_style('headless-blocked-page', $customCss);
        }
    }

    /**
     * Add admin dynamic styles
     */
    public function addAdminDynamicStyles(): void
    {
        if (!$this->isPluginAdminPage()) {
            return;
        }

        echo '<style id="headless-admin-dynamic">
        .headless-config-section {
            --headless-primary: #007cba;
            --headless-success: #46b450;
            --headless-warning: #ffb900;
            --headless-error: #dc3232;
            --headless-border-radius: 8px;
            --headless-box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        </style>';
    }

    /**
     * Add frontend dynamic styles
     */
    public function addFrontendDynamicStyles(): void
    {
        if (!$this->isBlockedPageContext()) {
            return;
        }

        $settings = $this->headlessHandler->get_settings();

        echo '<style id="headless-dynamic-vars">
        :root {
            --headless-primary-color: ' . esc_attr($settings['blocked_page_background_color'] ?? '#667eea') . ';
            --headless-gradient: ' . esc_attr($settings['blocked_page_background_gradient'] ?? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)') . ';
            --headless-icon-size: 80px;
            --headless-border-radius: 20px;
            --headless-container-bg: rgba(255, 255, 255, 0.95);
        }
        </style>';
    }

    /**
     * Generate custom CSS for blocked page
     *
     * @param array<string, mixed> $settings
     * @return string
     */
    private function generateBlockedPageCustomCss(array $settings): string
    {
        $customCss = $settings['blocked_page_custom_css'] ?? '';

        if (!empty($customCss)) {
            // Sanitize CSS
            $customCss = wp_strip_all_tags($customCss);
            $customCss = preg_replace('/[\\0-\\x1F\\x7F]/', '', $customCss);
            return '/* Custom CSS */ ' . $customCss;
        }

        return '';
    }

    /**
     * Generate hash for cache busting
     *
     * @param array<string, mixed> $settings
     * @return string
     */
    private function generateCssHash(array $settings): string
    {
        $cssSettings = [
            $settings['blocked_page_background_color'] ?? '',
            $settings['blocked_page_background_gradient'] ?? '',
            $settings['blocked_page_custom_css'] ?? ''
        ];

        return substr(md5(implode('|', $cssSettings)), 0, 8);
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
            !$this->headlessHandler->is_allowed_request();
    }

    /**
     * Get CSS for blocked page (for template usage)
     *
     * @return string
     */
    public function getBlockedPageCss(): string
    {
        $cssFile = $this->pluginPath . 'public/css/frontend/blocked-page.css';
        $baseCss = file_exists($cssFile) ? file_get_contents($cssFile) : '';

        $settings = $this->headlessHandler->get_settings();
        $customCss = $this->generateBlockedPageCustomCss($settings);

        return $baseCss . $customCss;
    }
}
