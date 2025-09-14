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
     * Flag to track initialization status
     *
     * @var bool
     */
    private $initialized = false;

    /**
     * Flag to track initialization in progress
     *
     * @var bool
     */
    private $initializing = false;

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
        // Empty constructor - deferred initialization
    }

    /**
     * Initialize the plugin
     */
    public function init(): void
    {
        if ($this->initialized) {
            return;
        }

        $this->initializing = true;

        try {
            // Initialize SettingsManager first
            $this->settingsManager = new SettingsManager();

            // Initialize the headless handler with SettingsManager
            $this->headlessHandler = new HeadlessHandler($this->settingsManager);

            // Initialize template system
            $this->templateRenderer = new TemplateRenderer(HEADLESS_WP_ADMIN_PLUGIN_DIR);

            // Register global components
            $this->registerComponents();

            // Initialize asset manager with SettingsManager instead of HeadlessHandler
            $this->assetManager = new AssetManager(
                $this->settingsManager,
                HEADLESS_WP_ADMIN_VERSION,
                HEADLESS_WP_ADMIN_PLUGIN_URL,
                HEADLESS_WP_ADMIN_PLUGIN_DIR
            );

            // Load textdomain on correct hook (init)
            $this->loadTextdomain();

            // Initialize components based on context
            $this->initializeContextSpecificComponents();

            // Configure admin interface
            $this->configureAdminInterface();

            // Set initialized flag AFTER everything is set up
            $this->initialized = true;
            $this->initializing = false;

            error_log('Headless WP Admin: Plugin initialized successfully');
        } catch (\Exception $e) {
            $this->initializing = false;
            error_log('Headless WP Admin Init Error: ' . $e->getMessage());
        }
    }

    /**
     * Initialize context-specific components
     */
    private function initializeContextSpecificComponents(): void
    {
        if (is_admin()) {
            new AdminPage($this->headlessHandler, $this->settingsManager);
        } else {
            new PublicInterface();
        }

        new SettingsEndpoint($this->settingsManager);
    }

    /**
     * Register global components
     */
    private function registerComponents(): void
    {
        $this->templateRenderer->registerComponent(
            'status-indicator',
            new StatusIndicator(false, '')
        );

        $this->templateRenderer->registerComponent(
            'button',
            new Button('', Button::TYPE_PRIMARY)
        );

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
        if (!($screen instanceof \WP_Screen) || $screen->id !== 'toplevel_page_headless-mode') {
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
     * Load plugin textdomain at the correct hook
     */
    public function loadTextdomain(): void
    {
        // Load textdomain on init hook to avoid early loading warning
        add_action('init', function () {
            load_plugin_textdomain(
                'headless-wp-admin',
                false,
                dirname(plugin_basename(HEADLESS_WP_ADMIN_PLUGIN_FILE)) . '/languages/'
            );
        });
    }

    /**
     * Get template renderer instance
     *
     * @return TemplateRenderer
     * @throws \RuntimeException Si el plugin no está inicializado
     */
    public function getTemplateRenderer(): TemplateRenderer
    {
        if (!$this->initialized && !$this->initializing) {
            throw new \RuntimeException('Plugin not initialized. Call init() first.');
        }
        if (null === $this->templateRenderer) {
            throw new \RuntimeException('TemplateRenderer not available');
        }
        return $this->templateRenderer;
    }

    /**
     * Get headless handler instance
     *
     * @return HeadlessHandler
     * @throws \RuntimeException Si el plugin no está inicializado
     */
    public function getHeadlessHandler(): HeadlessHandler
    {
        if (!$this->initialized && !$this->initializing) {
            throw new \RuntimeException('Plugin not initialized. Call init() first.');
        }
        return $this->headlessHandler;
    }

    /**
     * Get settings manager instance
     *
     * @return SettingsManager
     * @throws \RuntimeException Si el plugin no está inicializado
     */
    public function getSettingsManager(): SettingsManager
    {
        if (!$this->initialized && !$this->initializing) {
            throw new \RuntimeException('Plugin not initialized. Call init() first.');
        }
        return $this->settingsManager;
    }

    /**
     * Get asset manager instance
     *
     * @return AssetManager
     * @throws \RuntimeException Si el plugin no está inicializado
     */
    public function getAssetManager(): AssetManager
    {
        if (!$this->initialized && !$this->initializing) {
            throw new \RuntimeException('Plugin not initialized. Call init() first.');
        }
        return $this->assetManager;
    }

    /**
     * Check if plugin is initialized
     *
     * @return bool
     */
    public function isInitialized(): bool
    {
        return $this->initialized;
    }

    /**
     * Plugin activation hook
     */
    public static function activate(): void
    {
        flush_rewrite_rules();

        try {
            $settingsManager = new SettingsManager();
            $headlessHandler = new HeadlessHandler($settingsManager);
            $defaultSettings = $headlessHandler->get_settings();

            if (empty($settingsManager->get_settings())) {
                $settingsManager->update_settings($defaultSettings);
            }

            error_log('Headless WP Admin: Plugin activated successfully');
        } catch (\Exception $e) {
            error_log('Headless WP Admin Activation Error: ' . $e->getMessage());
        }
    }

    /**
     * Plugin deactivation hook
     */
    public static function deactivate(): void
    {
        flush_rewrite_rules();

        $timestamp = wp_next_scheduled('headless_cleanup_event');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'headless_cleanup_event');
        }

        error_log('Headless WP Admin: Plugin deactivated');
    }

    /**
     * Plugin uninstallation hook
     */
    public static function uninstall(): void
    {
        $settingsManager = new SettingsManager();
        $settingsManager->delete_settings();

        delete_option('headless_wp_cache');

        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }

        delete_transient('headless_api_status');
        delete_transient('headless_settings_hash');

        error_log('Headless WP Admin: Plugin uninstalled');
    }
}
