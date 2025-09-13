<?php

/**
 * Blocked Page Renderer for Headless WordPress Admin
 * Handles rendering of the blocked page for headless mode
 */

namespace HeadlessWPAdmin\Core;

use HeadlessWPAdmin\Core\TemplateSystem\TemplateRenderer;

class BlockedPageRenderer
{

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
     * Constructor
     *
     * @param SettingsManager $settingsManager
     */
    public function __construct(SettingsManager $settingsManager)
    {
        $this->settingsManager = $settingsManager;
        $this->templateRenderer = Plugin::getInstance()->getTemplateRenderer();
    }

    /**
     * Render the blocked page
     */
    public function render(): void
    {
        $this->sendHeaders();

        // Get settings for the blocked page
        $settings = $this->getPageSettings();

        // Additional context
        $context = [
            'settings' => $settings,
            'admin_url' => admin_url(),
            'graphql_url' => home_url('/graphql'),
            'is_graphql_enabled' => $this->settingsManager->get_setting('graphql_enabled', true),
            'is_rest_enabled' => $this->settingsManager->get_setting('rest_api_enabled', false),
        ];

        // Render the blocked page
        echo $this->templateRenderer->render('Frontend/blocked-page', $context);
        exit;
    }

    /**
     * Send appropriate HTTP headers
     */
    private function sendHeaders(): void
    {
        header('HTTP/1.1 403 Forbidden');
        header('Content-Type: text/html; charset=UTF-8');
        nocache_headers();
    }

    /**
     * Get page settings with defaults
     *
     * @return array<string, mixed>
     */
    private function getPageSettings(): array
    {
        return [
            'title' => $this->settingsManager->get_setting('blocked_page_title', 'Headless Mode Active'),
            'subtitle' => $this->settingsManager->get_setting('blocked_page_subtitle', ''),
            'message' => $this->settingsManager->get_setting(
                'blocked_page_message',
                'This WordPress site is running in headless mode. The frontend is disabled.'
            ),
            'icon' => $this->settingsManager->get_setting('blocked_page_icon', '🚀'),
            'background_color' => $this->settingsManager->get_setting('blocked_page_background_color', '#667eea'),
            'background_gradient' => $this->settingsManager->get_setting(
                'blocked_page_background_gradient',
                'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
            ),
            'logo_url' => $this->settingsManager->get_setting('blocked_page_logo_url', ''),
            'show_admin_link' => $this->settingsManager->get_setting('blocked_page_show_admin_link', true),
            'show_graphql_link' => $this->settingsManager->get_setting('blocked_page_show_graphql_link', true),
            'show_status_info' => $this->settingsManager->get_setting('blocked_page_show_status_info', true),
            'custom_css' => $this->settingsManager->get_setting('blocked_page_custom_css', ''),
        ];
    }
}
