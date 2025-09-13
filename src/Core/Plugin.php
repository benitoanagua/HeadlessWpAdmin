<?php

/**
 * Main Plugin Class for Headless WordPress Admin
 * Handles initialization, lifecycle, and core functionality
 */

namespace HeadlessWPAdmin\Core;

use HeadlessWPAdmin\Admin\AdminPage;
use HeadlessWPAdmin\API\SettingsEndpoint;
use HeadlessWPAdmin\Core\TemplateSystem\TemplateRenderer;
use HeadlessWPAdmin\Frontend\PublicInterface;
use HeadlessWPAdmin\Views\Components\StatusIndicator;
use HeadlessWPAdmin\Views\Components\Button;
use HeadlessWPAdmin\Views\Components\FormFieldCheckbox;
use HeadlessWPAdmin\Views\Components\FormFieldText;
use HeadlessWPAdmin\Views\Components\FormFieldTextarea;
use HeadlessWPAdmin\Views\Components\FormFieldColor;

class Plugin
{

    /**
     * Plugin instance
     *
     * @var Plugin|null
     */
    private static $instance = null;

    /**
     * Headless handler instance
     *
     * @var HeadlessHandler
     */
    private $headlessHandler;

    /**
     * Asset manager instance
     *
     * @var AssetManager
     */
    private $assetManager;

    /**
     * Settings manager instance
     *
     * @var SettingsManager
     */
    private $settingsManager;

    /**
     * Template renderer instance
     *
     * @var TemplateRenderer
     */
    private $templateRenderer;

    /**
     * Get plugin instance
     *
     * @return Plugin
     */
    public static function getInstance(): Plugin
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct()
    {
        $this->init();
    }

    /**
     * Initialize the plugin
     */
    private function init(): void
    {
        // Initialize SettingsManager first
        $this->settingsManager = new SettingsManager();

        // Initialize the headless handler with SettingsManager
        $this->headlessHandler = new HeadlessHandler($this->settingsManager);

        // Initialize template system
        $this->templateRenderer = new TemplateRenderer(HEADLESS_WP_ADMIN_PLUGIN_DIR);

        // Register global components
        $this->registerComponents();

        // Initialize asset manager
        $this->assetManager = new AssetManager(
            $this->headlessHandler,
            HEADLESS_WP_ADMIN_VERSION,
            HEADLESS_WP_ADMIN_PLUGIN_URL,
            HEADLESS_WP_ADMIN_PLUGIN_DIR
        );

        add_action('init', [$this, 'loadTextdomain']);

        // Initialize components
        if (is_admin()) {
            new AdminPage($this->headlessHandler, $this->settingsManager);
        } else {
            new PublicInterface();
        }

        // Initialize API endpoints
        new SettingsEndpoint($this->settingsManager);

        // Configure admin interface
        $this->configureAdminInterface();
    }

    /**
     * Register global components
     */
    private function registerComponents(): void
    {
        // Status indicator component
        $this->templateRenderer->registerComponent(
            'status-indicator',
            new StatusIndicator(false, '')
        );

        // Button component
        $this->templateRenderer->registerComponent(
            'button',
            new Button('', Button::TYPE_PRIMARY)
        );

        // Form field components
        $this->templateRenderer->registerComponent(
            'form-field-checkbox',
            new FormFieldCheckbox('', '', false, '', '')
        );

        $this->templateRenderer->registerComponent(
            'form-field-text',
            new FormFieldText('', '', '', '', 'text')
        );

        $this->templateRenderer->registerComponent(
            'form-field-textarea',
            new FormFieldTextarea('', '', '', '', 4)
        );

        $this->templateRenderer->registerComponent(
            'form-field-color',
            new FormFieldColor('', '', '', '')
        );
    }

    /**
     * Configure admin interface
     */
    private function configureAdminInterface(): void
    {
        add_action('admin_notices', [$this, 'adminNotices']);
        add_action('admin_bar_menu', [$this, 'modifyAdminBar'], 999);
        add_action('wp_dashboard_setup', [$this, 'addDashboardWidgets']);
    }

    /**
     * Display admin notices
     */
    public function adminNotices(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $screen = get_current_screen();
        if ($screen instanceof \WP_Screen && $screen->id !== 'toplevel_page_headless-mode') {
            return;
        }

        echo '<div class="notice notice-info">
            <h3>🚀 Headless WordPress Active</h3>
            <p>Fully customizable configuration available on this page.</p>
        </div>';
    }

    /**
     * Modify admin bar
     *
     * @param \WP_Admin_Bar $wpAdminBar
     */
    public function modifyAdminBar(\WP_Admin_Bar $wpAdminBar): void
    {
        $wpAdminBar->remove_node('view-site');

        if ($this->headlessHandler->get_setting('graphql_enabled')) {
            $wpAdminBar->add_node([
                'id' => 'graphql-endpoint',
                'title' => '⚡ GraphQL',
                'href' => home_url('/graphql'),
                'meta' => ['target' => '_blank']
            ]);
        }

        $wpAdminBar->add_node([
            'id' => 'headless-config',
            'title' => '🚀 Headless',
            'href' => admin_url('admin.php?page=headless-mode')
        ]);
    }

    /**
     * Add dashboard widgets
     */
    public function addDashboardWidgets(): void
    {
        wp_add_dashboard_widget(
            'headless_status',
            '🚀 Headless Status',
            [$this, 'dashboardWidget']
        );
    }

    /**
     * Render dashboard widget
     */
    public function dashboardWidget(): void
    {
        $settings = $this->headlessHandler->get_settings();

        echo '<div style="text-align: center;">
            <p><strong>Headless Configuration Active</strong></p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 15px 0;">
                <div>GraphQL: ' . ($settings['graphql_enabled'] ? '✅' : '❌') . '</div>
                <div>REST API: ' . ($settings['rest_api_enabled'] ? '✅' : '❌') . '</div>
                <div>Frontend: ❌ Blocked</div>
                <div>Admin: ✅ Active</div>
            </div>
            <p>
                <a href="' . admin_url('admin.php?page=headless-mode') . '" class="button button-primary">Configure</a>
                <a href="' . home_url('/') . '" target="_blank" class="button">View Blocked Page</a>
            </p>
        </div>';
    }

    /**
     * Load plugin textdomain
     */
    public function loadTextdomain(): void
    {
        load_plugin_textdomain(
            'headless-wp-admin',
            false,
            dirname(plugin_basename(HEADLESS_WP_ADMIN_PLUGIN_FILE)) . '/languages/'
        );
    }

    /**
     * Get template renderer instance
     *
     * @return TemplateRenderer
     */
    public function getTemplateRenderer(): TemplateRenderer
    {
        return $this->templateRenderer;
    }

    /**
     * Get headless handler instance
     *
     * @return HeadlessHandler
     */
    public function getHeadlessHandler(): HeadlessHandler
    {
        return $this->headlessHandler;
    }

    /**
     * Get settings manager instance
     *
     * @return SettingsManager
     */
    public function getSettingsManager(): SettingsManager
    {
        return $this->settingsManager;
    }

    /**
     * Get asset manager instance
     *
     * @return AssetManager
     */
    public function getAssetManager(): AssetManager
    {
        return $this->assetManager;
    }

    /**
     * Plugin activation hook
     */
    public static function activate(): void
    {
        // Activation code
        flush_rewrite_rules();

        // Create default options if they don't exist
        $settingsManager = new SettingsManager();
        $headlessHandler = new HeadlessHandler($settingsManager);
        $defaultSettings = $headlessHandler->get_settings();

        if (empty($settingsManager->get_settings())) {
            $settingsManager->update_settings($defaultSettings);
        }
    }

    /**
     * Plugin deactivation hook
     */
    public static function deactivate(): void
    {
        // Deactivation code
        flush_rewrite_rules();

        // Clean up cron jobs if necessary
        $timestamp = wp_next_scheduled('headless_cleanup_event');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'headless_cleanup_event');
        }
    }

    /**
     * Plugin uninstallation hook
     */
    public static function uninstall(): void
    {
        // Uninstallation code
        $settingsManager = new SettingsManager();
        $settingsManager->delete_settings();

        delete_option('headless_wp_cache');

        // Clean up any related cache
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }

        // Clean up transients if necessary
        delete_transient('headless_api_status');
        delete_transient('headless_settings_hash');
    }
}
