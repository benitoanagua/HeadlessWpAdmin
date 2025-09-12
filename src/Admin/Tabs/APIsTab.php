<?php

namespace HeadlessWPAdmin\Admin\Tabs;

class APIsTab
{

    public function __construct()
    {
        // Esta clase ahora es stateless, no necesita HeadlessHandler
    }

    /**
     * @param array<string, mixed> $settings
     */
    public function render(array $settings): void
    {
?>
        <div class="headless-config-section">
            <h3>⚡ GraphQL API</h3>

            <div class="headless-form-row">
                <label>
                    <span class="status-indicator <?php echo $settings['graphql_enabled'] ? 'status-active' : 'status-inactive'; ?>"></span>
                    <input type="checkbox" name="graphql_enabled" value="1" <?php checked($settings['graphql_enabled']); ?>>
                    Habilitar GraphQL API
                </label>
                <p class="description">
                    Endpoint: <code><?php echo home_url('/graphql'); ?></code>
                    <button type="button" id="test-graphql" class="button button-small">Probar</button>
                </p>
            </div>

            <div class="headless-form-row">
                <label>
                    <input type="checkbox" name="graphql_cors_enabled" value="1" <?php checked($settings['graphql_cors_enabled']); ?>>
                    Habilitar CORS para GraphQL
                </label>
            </div>

            <div class="headless-form-row">
                <label for="graphql_cors_origins">Orígenes CORS Permitidos (GraphQL)</label>
                <textarea name="graphql_cors_origins" id="graphql_cors_origins"><?php echo esc_textarea($settings['graphql_cors_origins']); ?></textarea>
                <p class="description">Un origen por línea (ej: https://localhost:3000)</p>
            </div>

            <div class="headless-grid">
                <div class="headless-form-row">
                    <label>
                        <input type="checkbox" name="graphql_introspection" value="1" <?php checked($settings['graphql_introspection']); ?>>
                        Habilitar introspección
                    </label>
                    <p class="description">Permite explorar el schema GraphQL</p>
                </div>

                <div class="headless-form-row">
                    <label>
                        <input type="checkbox" name="graphql_tracing" value="1" <?php checked($settings['graphql_tracing']); ?>>
                        Habilitar tracing
                    </label>
                    <p class="description">Para debugging y análisis de performance</p>
                </div>

                <div class="headless-form-row">
                    <label>
                        <input type="checkbox" name="graphql_caching" value="1" <?php checked($settings['graphql_caching']); ?>>
                        Habilitar caché de consultas
                    </label>
                    <p class="description">Mejora el rendimiento de GraphQL</p>
                </div>
            </div>
        </div>

        <div class="headless-config-section">
            <h3>🔌 REST API</h3>

            <div class="headless-form-row">
                <label>
                    <span class="status-indicator <?php echo $settings['rest_api_enabled'] ? 'status-active' : 'status-inactive'; ?>"></span>
                    <input type="checkbox" name="rest_api_enabled" value="1" <?php checked($settings['rest_api_enabled']); ?>>
                    Habilitar REST API
                </label>
                <p class="description">
                    Endpoint: <code><?php echo home_url('/wp-json/'); ?></code>
                </p>
            </div>

            <div class="headless-form-row">
                <label>
                    <input type="checkbox" name="rest_api_auth_required" value="1" <?php checked($settings['rest_api_auth_required']); ?>>
                    Requerir autenticación para REST API
                </label>
                <p class="description">Solo usuarios autenticados pueden acceder</p>
            </div>

            <div class="headless-form-row">
                <label for="rest_api_allowed_routes">Rutas REST Permitidas</label>
                <textarea name="rest_api_allowed_routes" id="rest_api_allowed_routes"><?php echo esc_textarea($settings['rest_api_allowed_routes']); ?></textarea>
                <p class="description">Una ruta por línea. Deja vacío para permitir todas.</p>
            </div>

            <div class="headless-form-row">
                <label for="rest_api_cors_origins">Orígenes CORS Permitidos (REST API)</label>
                <textarea name="rest_api_cors_origins" id="rest_api_cors_origins"><?php echo esc_textarea($settings['rest_api_cors_origins']); ?></textarea>
                <p class="description">Un origen por línea para configurar CORS</p>
            </div>
        </div>
<?php
    }
}
