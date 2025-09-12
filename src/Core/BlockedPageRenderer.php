<?php

namespace HeadlessWPAdmin\Core;

/**
 * Handles rendering of the blocked page for headless mode
 */
class BlockedPageRenderer
{

    /**
     * Settings manager instance
     *
     * @var SettingsManager
     */
    private $settings_manager;

    /**
     * Constructor
     *
     * @param SettingsManager $settings_manager
     */
    public function __construct(SettingsManager $settings_manager)
    {
        $this->settings_manager = $settings_manager;
    }

    /**
     * Render the blocked page
     */
    public function render(): void
    {
        $this->send_headers();
        $this->render_html();
        exit;
    }

    /**
     * Send appropriate HTTP headers
     */
    private function send_headers(): void
    {
        header('HTTP/1.1 403 Forbidden');
        header('Content-Type: text/html; charset=UTF-8');
        nocache_headers();
    }

    /**
     * Render the HTML content
     */
    private function render_html(): void
    {
        $settings = $this->get_page_settings();
        $css_url = $this->get_css_url();
?>
        <!DOCTYPE html>
        <html lang="<?php echo esc_attr(get_locale()); ?>">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo esc_html($settings['title']); ?></title>
            <meta name="robots" content="noindex,nofollow">
            <link rel="stylesheet" href="<?php echo esc_url($css_url); ?>">
            <style>
                <?php echo $this->get_custom_styles($settings); ?>
            </style>
        </head>

        <body>
            <div class="container">
                <?php $this->render_page_content($settings); ?>
            </div>
        </body>

        </html>
<?php
    }

    /**
     * Get page settings with defaults
     *
     * @return array<string, mixed>
     */
    private function get_page_settings(): array
    {
        return [
            'title' => $this->settings_manager->get_setting('blocked_page_title', 'Página Bloqueada'),
            'subtitle' => $this->settings_manager->get_setting('blocked_page_subtitle', ''),
            'message' => $this->settings_manager->get_setting(
                'blocked_page_message',
                'Este sitio WordPress funciona en modo headless. El frontend está deshabilitado.'
            ),
            'logo_url' => $this->settings_manager->get_setting('blocked_page_logo', ''),
            'background' => $this->settings_manager->get_setting('blocked_page_background', ''),
            'show_admin_link' => $this->settings_manager->get_setting('blocked_page_show_admin_link', true),
            'show_graphql_link' => $this->settings_manager->get_setting('blocked_page_show_graphql_link', true),
            'show_status_info' => $this->settings_manager->get_setting('blocked_page_show_status_info', true)
        ];
    }

    /**
     * Get CSS URL
     *
     * @return string
     */
    private function get_css_url(): string
    {
        return HEADLESS_WP_ADMIN_PLUGIN_URL . 'assets/css/frontend/blocked-page.css';
    }

    /**
     * Get custom styles from settings
     *
     * @param array<string, mixed> $settings
     * @return string
     */
    private function get_custom_styles(array $settings): string
    {
        $styles = '';
        if (!empty($settings['background'])) {
            $styles .= ":root { --headless-gradient: {$settings['background']}; }";
        }
        return $styles;
    }

    /**
     * Render page content
     *
     * @param array<string, mixed> $settings
     */
    private function render_page_content(array $settings): void
    {
        $this->render_logo($settings);
        $this->render_title($settings);
        $this->render_subtitle($settings);
        $this->render_message($settings);

        if ($settings['show_status_info']) {
            $this->render_status_info();
        }

        $this->render_buttons($settings);
    }

    /**
     * Render logo section
     *
     * @param array<string, mixed> $settings
     */
    private function render_logo(array $settings): void
    {
        if (!empty($settings['logo_url'])) {
            echo '<div class="logo">';
            echo '<img src="' . esc_url($settings['logo_url']) . '" alt="Logo" onerror="this.style.display=\'none\'">';
            echo '</div>';
        } else {
            echo '<div class="icon">🚀</div>';
        }
    }

    /**
     * Render title
     *
     * @param array<string, mixed> $settings
     */
    private function render_title(array $settings): void
    {
        echo '<h1>' . esc_html($settings['title']) . '</h1>';
    }

    /**
     * Render subtitle
     *
     * @param array<string, mixed> $settings
     */
    private function render_subtitle(array $settings): void
    {
        if (!empty($settings['subtitle'])) {
            echo '<p class="subtitle">' . esc_html($settings['subtitle']) . '</p>';
        }
    }

    /**
     * Render message
     *
     * @param array<string, mixed> $settings
     */
    private function render_message(array $settings): void
    {
        if (!empty($settings['message'])) {
            echo '<div class="message">';
            echo wp_kses_post(wpautop($settings['message']));
            echo '</div>';
        }
    }

    /**
     * Render status information
     */
    private function render_status_info(): void
    {
        echo '<div class="status-grid">';

        echo '<div class="status-item">';
        echo '<div class="status active">Modo Headless: ACTIVO</div>';
        echo '</div>';

        if ($this->settings_manager->get_setting('graphql_enabled', true)) {
            echo '<div class="status-item">';
            echo '<div class="status active">GraphQL: HABILITADO</div>';
            echo '</div>';
        }

        if ($this->settings_manager->get_setting('rest_api_enabled', false)) {
            echo '<div class="status-item">';
            echo '<div class="status active">REST API: HABILITADA</div>';
            echo '</div>';
        }

        echo '</div>';
    }

    /**
     * Render action buttons
     *
     * @param array<string, mixed> $settings
     */
    private function render_buttons(array $settings): void
    {
        echo '<div class="buttons">';

        if ($settings['show_admin_link']) {
            echo '<a href="' . esc_url(admin_url()) . '" class="btn">';
            echo '📊 Ir al Panel de Administración';
            echo '</a>';
        }

        if ($settings['show_graphql_link'] && $this->settings_manager->get_setting('graphql_enabled', true)) {
            echo '<a href="' . esc_url(home_url('/graphql')) . '" class="btn" target="_blank">';
            echo '⚡ GraphQL API';
            echo '</a>';
        }

        echo '</div>';
    }
}
