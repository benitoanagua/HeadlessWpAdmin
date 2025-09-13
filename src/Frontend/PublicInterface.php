<?php

/**
 * Public Interface Handler for Headless WordPress Admin
 * Handles frontend rendering and shortcodes
 */

namespace HeadlessWPAdmin\Frontend;

class PublicInterface
{

    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('wp_footer', [$this, 'renderFrontendApp']);
        add_shortcode('headless-wp-admin', [$this, 'renderShortcode']);
    }

    /**
     * Render frontend app (if needed)
     */
    public function renderFrontendApp(): void
    {
        // Render frontend app if necessary
        // This can be used for any frontend JavaScript applications
    }

    /**
     * Render shortcode
     *
     * @param array<string, mixed> $atts Shortcode attributes
     * @return string
     */
    public function renderShortcode(array $atts = []): string
    {
        $atts = shortcode_atts([
            'type' => 'default'
        ], $atts);

        return '<div id="headless-wp-admin-shortcode" data-type="' . esc_attr($atts['type']) . '"></div>';
    }
}
