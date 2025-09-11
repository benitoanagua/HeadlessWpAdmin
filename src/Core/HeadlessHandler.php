<?php

namespace HeadlessWPAdmin\Core;

/**
 * Clase para manejar la lógica headless
 */
class HeadlessHandler
{
    private string $option_name = 'headless_wp_settings';

    /**
     * @var array<string, mixed>
     */
    private array $default_settings = [];

    public function __construct()
    {
        $this->set_default_settings();
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

        $this->configure_rest_api();
        $this->configure_graphql();
        $this->configure_security();
        $this->cleanup_wordpress_features();
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

    private function is_allowed_request(): bool
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

        // Construir estilos dinámicos
        $custom_styles = $this->build_custom_styles($settings);

        $html = '<!DOCTYPE html>
<html lang="' . get_locale() . '">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . esc_html($settings['blocked_page_title']) . ' - ' . esc_html($site_name) . '</title>
    <meta name="robots" content="noindex,nofollow">
    <style>' . $custom_styles . '</style>
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

    /**
     * @param array<string, mixed> $settings
     */
    private function build_custom_styles(array $settings): string
    {
        $bg_gradient = !empty($settings['blocked_page_background_gradient'])
            ? $settings['blocked_page_background_gradient']
            : 'linear-gradient(135deg, ' . $settings['blocked_page_background_color'] . ' 0%, #764ba2 100%)';

        $styles = '
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: ' . $bg_gradient . ';
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2c3e50;
            line-height: 1.6;
        }
        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 700px;
            width: 90%;
            text-align: center;
            animation: slideUp 0.6s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .logo { margin-bottom: 20px; }
        .logo img { max-width: 150px; height: auto; border-radius: 10px; }
        .icon {
            font-size: 80px;
            margin-bottom: 20px;
            display: block;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #2c3e50;
            font-weight: 700;
        }
        .subtitle {
            font-size: 1.3rem;
            color: #7f8c8d;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .message {
            font-size: 1.1rem;
            color: #5a6c7d;
            margin-bottom: 30px;
            line-height: 1.7;
        }
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .status-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        .status-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .status {
            font-weight: bold;
            font-size: 1.1em;
            margin-bottom: 5px;
        }
        .status.active { color: #28a745; }
        .status.blocked { color: #dc3545; }
        .endpoint {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 25px;
            margin: 25px 0;
            position: relative;
            overflow: hidden;
        }
        .endpoint::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: ' . $bg_gradient . ';
        }
        .endpoint strong {
            display: block;
            margin-bottom: 15px;
            color: #495057;
            font-size: 1.1rem;
        }
        .endpoint code {
            background: #e9ecef;
            padding: 10px 15px;
            border-radius: 8px;
            color: #495057;
            word-break: break-all;
            font-size: 0.95rem;
            display: block;
            font-family: "SF Mono", Monaco, monospace;
        }
        .buttons {
            margin-top: 30px;
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 25px;
            background: ' . $bg_gradient . ';
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            font-size: 1.rem;
        }
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .contact-info {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 15px;
            border-left: 4px solid #007cba;
        }
        .footer {
            margin-top: 30px;
            font-size: 0.9rem;
            color: #6c757d;
        }
        @media (max-width: 768px) {
            .container { padding: 30px 20px; }
            h1 { font-size: 2rem; }
            .buttons { flex-direction: column; align-items: center; }
            .btn { width: 100%; max-width: 280px; justify-content: center; }
            .status-grid { grid-template-columns: 1fr; }
        }
        ' . $settings['blocked_page_custom_css'];

        return $styles;
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
                $content .= '<div class::status-item">
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

    private function configure_rest_api(): void
    {
        if (!$this->get_setting('rest_api_enabled')) {
            add_filter('rest_enabled', '__return_false');
            add_filter('rest_jsonp_enabled', '__return_false');

            add_filter('rest_authentication_errors', function ($result) {
                if (is_admin() || current_user_can('manage_options')) {
                    return $result;
                }
                return new \WP_Error('rest_disabled', 'REST API deshabilitada en configuración headless', ['status' => 403]);
            });

            remove_action('wp_head', 'rest_output_link_wp_head');
            remove_action('template_redirect', 'rest_output_link_header', 11);
        } else {
            // Configurar CORS para REST API
            $this->setup_rest_cors();

            // Autenticación requerida si está configurada
            if ($this->get_setting('rest_api_auth_required')) {
                $this->setup_rest_auth();
            }

            // Filtrar rutas permitidas
            $this->filter_rest_routes();
        }
    }

    private function setup_rest_cors(): void
    {
        add_action('rest_api_init', function () {
            $origins = array_filter(explode("\n", $this->get_setting('rest_api_cors_origins')));

            remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
            add_filter('rest_pre_serve_request', function ($value) use ($origins) {
                $origin = get_http_origin();
                if ($origin && in_array($origin, $origins)) {
                    header('Access-Control-Allow-Origin: ' . esc_url_raw($origin));
                    header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
                    header('Access-Control-Allow-Credentials: true');
                    header('Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce');
                }
                return $value;
            });
        });
    }

    private function setup_rest_auth(): void
    {
        add_filter('rest_authentication_errors', function ($result) {
            if (!empty($result)) {
                return $result;
            }

            if (!is_user_logged_in() && !current_user_can('read')) {
                return new \WP_Error('rest_forbidden', 'Autenticación requerida', ['status' => 401]);
            }

            return $result;
        });
    }

    private function filter_rest_routes(): void
    {
        $allowed_routes = array_filter(explode("\n", $this->get_setting('rest_api_allowed_routes')));

        if (!empty($allowed_routes)) {
            add_filter('rest_pre_dispatch', function ($result, $request, $route) use ($allowed_routes) {
                $allowed = false;
                foreach ($allowed_routes as $allowed_route) {
                    if (strpos($route, trim($allowed_route)) !== false) {
                        $allowed = true;
                        break;
                    }
                }

                if (!$allowed) {
                    return new \WP_Error('rest_route_forbidden', 'Ruta no permitida en configuración headless', ['status' => 403]);
                }

                return $result;
            }, 10, 3);
        }
    }

    private function configure_graphql(): void
    {
        if (!$this->get_setting('graphql_enabled')) {
            // Deshabilitar GraphQL si está desactivado
            add_filter('graphql_enabled', '__return_false');
            return;
        }

        // CORS para GraphQL
        if ($this->get_setting('graphql_cors_enabled')) {
            add_filter('graphql_cors_allowed_origins', function ($origins) {
                $custom_origins = array_filter(explode("\n", $this->get_setting('graphql_cors_origins')));
                return array_merge($origins, $custom_origins);
            });

            add_filter('graphql_cors_allow_credentials', '__return_true');

            add_filter('graphql_cors_allowed_headers', function ($headers) {
                return array_merge($headers, [
                    'Authorization',
                    'Content-Type',
                    'X-Requested-With',
                    'X-WP-Nonce',
                    'Cache-Control',
                    'Accept-Language',
                    'Apollo-Require-Preflight'
                ]);
            });
        }

        // Introspección
        add_filter('graphql_introspection_enabled', function () {
            return $this->get_setting('graphql_introspection');
        });

        // Tracing
        add_filter('graphql_tracing_enabled', function () {
            return $this->get_setting('graphql_tracing');
        });

        // Caching
        if ($this->get_setting('graphql_caching')) {
            add_filter('graphql_query_cache_enabled', '__return_true');
        }
    }

    private function configure_security(): void
    {
        if ($this->get_setting('security_headers_enabled')) {
            add_action('send_headers', function () {
                if (!is_admin()) {
                    header('X-Content-Type-Options: nosniff');
                    header('X-Frame-Options: SAMEORIGIN');
                    header('X-XSS-Protection: 1; mode=block');
                    header('Referrer-Policy: strict-origin-when-cross-origin');
                }
            });
        }

        if ($this->get_setting('block_theme_access')) {
            add_action('init', function () {
                if (!is_admin() && !defined('DOING_AJAX')) {
                    $request = $_SERVER['REQUEST_URI'] ?? '';
                    if (preg_match('/\/(wp-content\/themes\/.*\.php)/', $request)) {
                        wp_die('Acceso denegado', 'Error 403', ['response' => 403]);
                    }
                }
            });
        }
    }

    private function cleanup_wordpress_features(): void
    {
        if ($this->get_setting('disable_feeds')) {
            add_action('do_feed', [$this, 'disable_feeds'], 1);
            add_action('do_feed_rdf', [$this, 'disable_feeds'], 1);
            add_action('do_feed_rss', [$this, 'disable_feeds'], 1);
            add_action('do_feed_rss2', [$this, 'disable_feeds'], 1);
            add_action('do_feed_atom', [$this, 'disable_feeds'], 1);
        }

        if ($this->get_setting('disable_sitemaps')) {
            add_filter('wp_sitemaps_enabled', '__return_false');
        }

        if ($this->get_setting('disable_comments')) {
            add_filter('comments_open', '__return_false', 20, 2);
            add_filter('pings_open', '__return_false', 20, 2);
            remove_menu_page('edit-comments.php');
        }

        if ($this->get_setting('clean_wp_head')) {
            remove_action('wp_head', 'rsd_link');
            remove_action('wp_head', 'wlwmanifest_link');
            remove_action('wp_head', 'wp_generator');
            remove_action('wp_head', 'wp_shortlink_wp_head');
        }

        if ($this->get_setting('disable_emojis')) {
            remove_action('wp_head', 'print_emoji_detection_script', 7);
            remove_action('wp_print_styles', 'print_emoji_styles');
        }

        if ($this->get_setting('disable_embeds')) {
            remove_action('wp_head', 'wp_oembed_add_discovery_links');
            remove_action('wp_head', 'wp_oembed_add_host_js');
        }
    }

    public function disable_feeds(): void
    {
        wp_die('Feeds deshabilitados en configuración headless', 'Feeds No Disponibles', ['response' => 403]);
    }
}
