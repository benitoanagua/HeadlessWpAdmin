<?php

namespace HeadlessWPAdmin\API;

use HeadlessWPAdmin\Core\SettingsManager;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Clase para manejar el endpoint REST de configuración
 */
class SettingsEndpoint
{
    private SettingsManager $settingsManager;

    public function __construct(SettingsManager $settingsManager)
    {
        $this->settingsManager = $settingsManager;
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes(): void
    {
        register_rest_route('headless/v1', '/config', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_config'],
                'permission_callback' => [$this, 'check_permissions'],
            ],
            [
                'methods' => 'POST',
                'callback' => [$this, 'update_config'],
                'permission_callback' => [$this, 'check_permissions'],
            ]
        ]);
    }

    public function get_config(): WP_REST_Response
    {
        $settings = $this->settingsManager->get_settings();

        return rest_ensure_response([
            'success' => true,
            'data' => $settings,
            'endpoints' => [
                'graphql' => home_url('/graphql'),
                'rest' => home_url('/wp-json/'),
                'admin' => admin_url(),
            ]
        ]);
    }

    /**
     * @param WP_REST_Request<array<string, mixed>> $request
     */
    public function update_config(WP_REST_Request $request): WP_REST_Response
    {
        $new_settings = $request->get_json_params();

        // Usar SettingsManager para validar y guardar
        $result = $this->settingsManager->update_settings($new_settings);

        if ($result) {
            return rest_ensure_response([
                'success' => true,
                'message' => 'Configuración actualizada correctamente'
            ]);
        }

        // Para enviar error con código de estado, usar WP_Error
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Error al actualizar la configuración'
        ], 500);
    }

    public function check_permissions(): bool
    {
        return current_user_can('manage_options');
    }
}
