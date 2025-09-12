<?php

namespace HeadlessWPAdmin\Core\Services;

use HeadlessWPAdmin\Core\HeadlessHandler;

class RESTService
{
    private HeadlessHandler $headlessHandler;

    public function __construct(HeadlessHandler $headlessHandler)
    {
        $this->headlessHandler = $headlessHandler;
    }

    public function configure(): void
    {
        if (!$this->headlessHandler->get_setting('rest_api_enabled')) {
            $this->disable_rest_api();
        } else {
            $this->enable_rest_api();
        }
    }

    private function disable_rest_api(): void
    {
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
    }

    private function enable_rest_api(): void
    {
        // Configurar CORS para REST API
        $this->setup_rest_cors();

        // Autenticación requerida si está configurada
        if ($this->headlessHandler->get_setting('rest_api_auth_required')) {
            $this->setup_rest_auth();
        }

        // Filtrar rutas permitidas
        $this->filter_rest_routes();
    }

    private function setup_rest_cors(): void
    {
        add_action('rest_api_init', function () {
            $origins = array_filter(explode("\n", $this->headlessHandler->get_setting('rest_api_cors_origins')));

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
        $allowed_routes = array_filter(explode("\n", $this->headlessHandler->get_setting('rest_api_allowed_routes')));

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
}
