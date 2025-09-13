<?php

/**
 * Admin Page Handler for Headless WordPress Admin
 * Handles admin menu, page rendering, and settings management
 */

namespace HeadlessWPAdmin\Admin;

use HeadlessWPAdmin\Core\HeadlessHandler;
use HeadlessWPAdmin\Core\SettingsManager;
use HeadlessWPAdmin\Core\Plugin;
use HeadlessWPAdmin\Core\TemplateSystem\TemplateRenderer;

class AdminPage
{

    /**
     * Headless handler instance
     *
     * @var HeadlessHandler
     */
    private $headlessHandler;

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
     * Admin tabs
     *
     * @var array<string, string>
     */
    private $tabs;

    /**
     * Constructor
     *
     * @param HeadlessHandler $headlessHandler
     * @param SettingsManager $settingsManager
     */
    public function __construct(HeadlessHandler $headlessHandler, SettingsManager $settingsManager)
    {
        $this->headlessHandler = $headlessHandler;
        $this->settingsManager = $settingsManager;
        $this->templateRenderer = Plugin::getInstance()->getTemplateRenderer();

        // Define admin tabs
        $this->tabs = [
            'general' => __('General', 'headless-wp-admin'),
            'apis' => __('APIs', 'headless-wp-admin'),
            'blocked-page' => __('Blocked Page', 'headless-wp-admin'),
            'security' => __('Security', 'headless-wp-admin'),
            'advanced' => __('Advanced', 'headless-wp-admin'),
        ];

        $this->init();
    }

    /**
     * Initialize admin page
     */
    private function init(): void
    {
        add_action('admin_menu', [$this, 'addAdminMenu']);
        add_action('admin_init', [$this, 'adminInit']);
        add_action('wp_ajax_headless_test_endpoint', [$this, 'testEndpoint']);
        add_action('wp_ajax_headless_reset_settings', [$this, 'resetSettings']);
    }

    /**
     * Add admin menu
     */
    public function addAdminMenu(): void
    {
        // Main menu - Headless Configuration
        add_menu_page(
            __('Headless Configuration', 'headless-wp-admin'),
            __('Headless WordPress', 'headless-wp-admin'),
            'manage_options',
            'headless-mode',
            [$this, 'renderHeadlessConfigPage'],
            'dashicons-admin-generic',
            30
        );

        // Rename the first submenu to avoid duplication
        global $submenu;
        if (isset($submenu['headless-mode'])) {
            $submenu['headless-mode'][0][0] = __('Configuration', 'headless-wp-admin');
        }
    }

    /**
     * Admin initialization
     */
    public function adminInit(): void
    {
        register_setting('headless_wp_settings', 'headless_wp_settings');
    }

    /**
     * Render headless configuration page
     */
    public function renderHeadlessConfigPage(): void
    {
        // Load necessary WordPress assets
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

        if (isset($_POST['submit'])) {
            $this->saveSettings();
        }

        $settings = $this->settingsManager->get_settings();
        $active_tab = $_GET['tab'] ?? 'general';

        // Render tab content
        $tab_content = $this->templateRenderer->render("Admin/tabs/{$active_tab}", [
            'settings' => $settings
        ]);

        // Render main layout
        echo $this->templateRenderer->render('Admin/layouts/main', [
            'settings' => $settings,
            'active_tab' => $active_tab,
            'tabs' => $this->tabs,
            'content' => $tab_content
        ]);
    }

    /**
     * Save settings from form submission
     */
    private function saveSettings(): void
    {
        if (!wp_verify_nonce($_POST['headless_nonce'], 'headless_settings_save')) {
            wp_die('Security error');
        }

        if (!current_user_can('manage_options')) {
            wp_die('No permissions');
        }

        $new_settings = [];
        $default_settings = $this->headlessHandler->get_settings();

        // Process all form fields
        foreach ($default_settings as $key => $default_value) {
            if (isset($_POST[$key])) {
                $value = $_POST[$key];

                // Sanitize based on field type
                if (is_bool($default_value)) {
                    $new_settings[$key] = !empty($value);
                } elseif (filter_var($default_value, FILTER_VALIDATE_URL)) {
                    $new_settings[$key] = esc_url_raw($value);
                } elseif (in_array($key, ['blocked_page_custom_css', 'custom_redirect_rules', 'custom_headers'])) {
                    $new_settings[$key] = wp_unslash($value); // Allow CSS/code
                } else {
                    $new_settings[$key] = sanitize_textarea_field($value);
                }
            } else {
                // For unchecked checkboxes
                if (is_bool($default_value)) {
                    $new_settings[$key] = false;
                }
            }
        }

        // Use SettingsManager to save
        $this->settingsManager->update_settings($new_settings);

        echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'headless-wp-admin') . '</p></div>';
    }

    /**
     * Test endpoint via AJAX
     */
    public function testEndpoint(): void
    {
        $endpoint = $_POST['endpoint'] ?? '';

        if ($endpoint === 'graphql') {
            $response = wp_remote_get(home_url('/graphql'));
            if (!is_wp_error($response)) {
                wp_send_json_success('GraphQL endpoint is responding correctly');
            } else {
                wp_send_json_error('Error connecting to GraphQL: ' . $response->get_error_message());
            }
        }

        wp_send_json_error('Invalid endpoint');
    }

    /**
     * Reset settings via AJAX
     */
    public function resetSettings(): void
    {
        if (!wp_verify_nonce($_POST['nonce'], 'headless_reset')) {
            wp_send_json_error('Invalid nonce');
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error('No permissions');
        }

        // Use SettingsManager to reset
        $this->settingsManager->delete_settings();
        wp_send_json_success('Settings reset');
    }
}
