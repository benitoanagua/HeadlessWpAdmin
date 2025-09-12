<?php

namespace HeadlessWPAdmin\Core;

use HeadlessWPAdmin\Core\Services\RESTService;
use HeadlessWPAdmin\Core\Services\GraphQLService;
use HeadlessWPAdmin\Core\Services\SecurityService;
use HeadlessWPAdmin\Core\Services\CleanupService;

/**
 * Clase principal para manejar la lógica headless
 */
class HeadlessHandler
{
    private string $option_name = 'headless_wp_settings';

    /** 
     * @var array<string, mixed> Configuración predeterminada del plugin
     */
    private array $default_settings = [];

    private RESTService $restService;
    private GraphQLService $graphqlService;
    private SecurityService $securityService;
    private CleanupService $cleanupService;

    public function __construct()
    {
        $this->set_default_settings();
        $this->restService = new RESTService($this);
        $this->graphqlService = new GraphQLService($this);
        $this->securityService = new SecurityService($this);
        $this->cleanupService = new CleanupService($this);

        add_action('init', [$this, 'init_headless_mode'], 0);
        add_action('template_redirect', [$this, 'handle_frontend_requests'], 0);
    }

    private function set_default_settings(): void
    {
        $this->default_settings = [
            'rest_api_enabled' => false,
            'rest_api_auth_required' => true,
            'rest_api_allowed_routes' => "wp/v2/posts\nwp/v2/pages\nwp/v2/media",
            'rest_api_cors_origins' => "http://localhost:3000\nhttps://yourdomain.com",
            'graphql_enabled' => true,
            'graphql_cors_enabled' => true,
            'graphql_cors_origins' => "http://localhost:3000\nhttp://localhost:3001\nhttps://studio.apollographql.com",
            'graphql_introspection' => true,
            'graphql_tracing' => false,
            'graphql_caching' => true,
            'blocked_page_enabled' => true,
            'blocked_page_title' => 'Headless Mode Activo',
            'blocked_page_subtitle' => 'Este sitio funciona como backend headless',
            'blocked_page_message' => 'El frontend público está deshabilitado. Accede al panel de administración o utiliza la API GraphQL.',
            'blocked_page_icon' => '🚀',
            'blocked_page_background_color' => '#667eea',
            'blocked_page_background_gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
            'blocked_page_show_admin_link' => true,
            'blocked_page_show_graphql_link' => true,
            'blocked_page_show_status_info' => true,
            'blocked_page_custom_css' => '',
            'blocked_page_logo_url' => '',
            'blocked_page_contact_info' => '',
            'allowed_paths' => "/wp-admin/\n/wp-login.php\n/wp-cron.php\n/graphql\n/wp-admin/admin-ajax.php",
            'media_access_enabled' => true,
            'preview_access_enabled' => true,
            'security_headers_enabled' => true,
            'rate_limiting_enabled' => false,
            'debug_logging' => false,
            'block_theme_access' => true,
            'disable_feeds' => true,
            'disable_sitemaps' => true,
            'disable_comments' => true,
            'disable_embeds' => true,
            'disable_emojis' => true,
            'clean_wp_head' => true,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function get_settings(): array
    {
        $settings = get_option($this->option_name, []);
        return wp_parse_args($settings, $this->default_settings);
    }

    /**
     * @return mixed
     */
    public function get_setting(string $key, mixed $default = null): mixed
    {
        $settings = $this->get_settings();
        return isset($settings[$key]) ? $settings[$key] : ($default ?? $this->default_settings[$key] ?? '');
    }

    public function init_headless_mode(): void
    {
        if ($this->get_setting('debug_logging')) {
            error_log('Headless: Processing request: ' . ($_SERVER['REQUEST_URI'] ?? ''));
        }

        $this->restService->configure();
        $this->graphqlService->configure();
        $this->securityService->configure();
        $this->cleanupService->configure();
    }

    public function handle_frontend_requests(): void
    {
        if ($this->is_allowed_request()) {
            return;
        }

        if ($this->get_setting('debug_logging')) {
            error_log('Headless: Blocked request to: ' . ($_SERVER['REQUEST_URI'] ?? '') . ' from ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        }

        $this->handle_blocked_request();
    }

    public function is_allowed_request(): bool
    {
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';

        // Paths permitidos configurables
        $allowed_paths = array_filter(explode("\n", $this->get_setting('allowed_paths')));

        foreach ($allowed_paths as $path) {
            $path = trim($path);
            if (!empty($path) && strpos($request_uri, $path) !== false) {
                return true;
            }
        }

        // Contextos especiales
        if (
            is_admin() ||
            (defined('DOING_AJAX') && DOING_AJAX) ||
            (defined('DOING_CRON') && DOING_CRON) ||
            (defined('WP_CLI') && WP_CLI)
        ) {
            return true;
        }

        // Acceso to media
        if ($this->get_setting('media_access_enabled') && $this->is_media_request($request_uri)) {
            return true;
        }

        // Preview para usuarios autenticados
        if ($this->get_setting('preview_access_enabled') && is_user_logged_in() && current_user_can('edit_posts')) {
            if (isset($_GET['preview']) || isset($_GET['p']) || isset($_GET['page_id'])) {
                return true;
            }
        }

        return false;
    }

    private function is_media_request(string $uri): bool
    {
        return preg_match('/\/wp-content\/uploads\/.*\.(jpg|jpeg|png|gif|svg|webp|pdf|doc|docx|mp4|mp3)$/i', $uri) === 1;
    }

    private function handle_blocked_request(): void
    {
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';

        // Homepage redirect
        if ($request_uri === '/' || $request_uri === '' || $request_uri === '/index.php') {
            wp_redirect(admin_url());
            exit();
        }

        // Mostrar página personalizada
        if ($this->get_setting('blocked_page_enabled')) {
            $this->show_custom_blocked_page();
        } else {
            wp_die('Acceso denegado - Sitio en modo headless', 'Acceso Denegado', ['response' => 403]);
        }
    }

    private function show_custom_blocked_page(): void
    {
        status_header(403);
        nocache_headers();

        $settings = $this->get_settings();
        $admin_url = admin_url();
        $graphql_url = home_url('/graphql');
        $site_name = get_bloginfo('name');

        header('Content-Type: text/html; charset=UTF-8');

        $html = '<!DOCTYPE html>
            <html lang="' . get_locale() . '">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>' . esc_html($settings['blocked_page_title']) . ' - ' . esc_html($site_name) . '</title>
                <meta name="robots" content="noindex,nofollow">
                <style>' . $this->get_blocked_page_css() . '</style>
            </head>
            <body>
                <div class="container">
                    ' . $this->build_page_content($settings, $admin_url, $graphql_url, $site_name) . '
                </div>
            </body>
            </html>';

        echo $html;
        exit();
    }

    private function get_blocked_page_css(): string
    {
        $css_file = HEADLESS_WP_ADMIN_PLUGIN_DIR . 'assets/css/frontend/blocked-page.css';

        // Leer CSS base de forma segura
        $base_css = '';
        if (file_exists($css_file) && is_readable($css_file)) {
            $base_css_content = file_get_contents($css_file);
            $base_css = is_string($base_css_content) ? $base_css_content : '';
        }

        $settings = $this->get_settings();
        $custom_css = $settings['blocked_page_custom_css'] ?? '';

        // Procesar CSS personalizado de forma segura
        if (!empty($custom_css) && is_string($custom_css)) {
            // Sanitizar CSS
            $sanitized_css = wp_strip_all_tags($custom_css);

            // Eliminar caracteres de control de forma segura
            $cleaned_css = preg_replace('/[\\0-\\x1F\\x7F]/', '', $sanitized_css);
            if ($cleaned_css === null) {
                // Si preg_replace falla, usar la versión sanitizada sin limpiar
                $cleaned_css = $sanitized_css;
            }

            $custom_css = '/* Custom CSS */ ' . $cleaned_css;
        } else {
            $custom_css = '';
        }

        return $base_css . $custom_css;
    }

    /**
     * @param array<string, mixed> $settings
     */
    private function build_page_content(array $settings, string $admin_url, string $graphql_url, string $site_name): string
    {
        $content = '';

        // Logo
        if (!empty($settings['blocked_page_logo_url'])) {
            $content .= '<div class="logo"><img src="' . esc_url($settings['blocked_page_logo_url']) . '" alt="Logo"></div>';
        }

        // Icono
        if (!empty($settings['blocked_page_icon'])) {
            $content .= '<div class="icon">' . esc_html($settings['blocked_page_icon']) . '</div>';
        }

        // Título y subtítulo
        $content .= '<h1>' . esc_html($settings['blocked_page_title']) . '</h1>';
        if (!empty($settings['blocked_page_subtitle'])) {
            $content .= '<p class="subtitle">' . esc_html($settings['blocked_page_subtitle']) . '</p>';
        }

        // Mensaje
        if (!empty($settings['blocked_page_message'])) {
            $content .= '<div class="message">' . wp_kses_post(wpautop($settings['blocked_page_message'])) . '</div>';
        }

        // Info de estado
        if ($settings['blocked_page_show_status_info']) {
            $content .= '<div class="status-grid">';

            if ($this->get_setting('graphql_enabled')) {
                $content .= '<div class="status-item">
                    <div class="status active">✅ GraphQL API</div>
                    <small>Disponible para consultas</small>
                </div>';
            }

            if ($this->get_setting('rest_api_enabled')) {
                $content .= '<div class="status-item">
                    <div class="status active">✅ REST API</div>
                    <small>Disponible con configuración</small>
                </div>';
            }

            $content .= '<div class="status-item">
                <div class="status active">✅ Administración</div>
                <small>Panel de control activo</small>
            </div>';

            $content .= '<div class="status-item">
                <div class="status blocked">❌ Frontend</div>
                <small>Público deshabilitado</small>
            </div>';

            $content .= '</div>';
        }

        // Endpoint GraphQL
        if ($settings['blocked_page_show_graphql_link'] && $this->get_setting('graphql_enabled')) {
            $content .= '<div class="endpoint">
                <strong>🔌 GraphQL Endpoint</strong>
                <code>' . esc_url($graphql_url) . '</code>
            </div>';
        }

        // Botones
        $content .= '<div class="buttons">';
        if ($settings['blocked_page_show_admin_link']) {
            $content .= '<a href="' . esc_url($admin_url) . '" class="btn">
                <span>⚙️</span> Administración
            </a>';
        }
        if ($settings['blocked_page_show_graphql_link'] && $this->get_setting('graphql_enabled')) {
            $content .= '<a href="' . esc_url($graphql_url) . '" class="btn" target="_blank">
                <span>⚡</span> GraphQL
            </a>';
        }
        $content .= '</div>';

        // Información de contacto
        if (!empty($settings['blocked_page_contact_info'])) {
            $content .= '<div class="contact-info">' . wp_kses_post(wpautop($settings['blocked_page_contact_info'])) . '</div>';
        }

        // Footer
        $content .= '<div class="footer">
            <p><strong>' . esc_html($site_name) . '</strong> - Headless CMS</p>
            <small>Desarrolla tu frontend usando las APIs disponibles</small>
        </div>';

        return $content;
    }
}
