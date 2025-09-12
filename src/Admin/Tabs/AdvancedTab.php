<?php

namespace HeadlessWPAdmin\Admin\Tabs;

class AdvancedTab
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
            <h3>⚙️ Configuración Avanzada</h3>

            <div class="headless-form-row">
                <label for="custom_redirect_rules">Reglas de Redirección Personalizadas</label>
                <textarea name="custom_redirect_rules" id="custom_redirect_rules" rows="6" placeholder="# Ejemplo:&#10;/old-page -> /new-page&#10;/blog/* -> https://external-blog.com/*"><?php echo esc_textarea($settings['custom_redirect_rules'] ?? ''); ?></textarea>
                <p class="description">Una regla por línea. Usa -> para separar origen y destino</p>
            </div>

            <div class="headless-form-row">
                <label for="custom_headers">Headers HTTP Personalizados</label>
                <textarea name="custom_headers" id="custom_headers" rows="4" placeholder="X-Custom-Header: value&#10;Cache-Control: no-cache"><?php echo esc_textarea($settings['custom_headers'] ?? ''); ?></textarea>
                <p class="description">Un header por línea en formato "Nombre: Valor"</p>
            </div>

            <div class="headless-form-row">
                <label for="webhook_urls">URLs de Webhooks</label>
                <textarea name="webhook_urls" id="webhook_urls" rows="3" placeholder="https://api.example.com/webhook1&#10;https://api.example.com/webhook2"><?php echo esc_textarea($settings['webhook_urls'] ?? ''); ?></textarea>
                <p class="description">Se llamarán cuando se actualice contenido (experimental)</p>
            </div>
        </div>

        <div class="headless-config-section">
            <h3>🔧 Herramientas de Mantenimiento</h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <button type="button" class="button" onclick="if(confirm('¿Limpiar cache de GraphQL?')) alert('Cache limpiado')">🗑️ Limpiar Cache GraphQL</button>
                <button type="button" class="button" onclick="window.open('<?php echo home_url('/wp-json/'); ?>', '_blank')">🔍 Explorar REST API</button>
                <button type="button" class="button" onclick="window.open('<?php echo home_url('/graphql'); ?>', '_blank')">⚡ Abrir GraphQL</button>
                <button type="button" class="button" onclick="if(confirm('¿Exportar configuración?')) alert('Función en desarrollo')">📤 Exportar Config</button>
            </div>
        </div>

        <div class="headless-config-section">
            <h3>📋 Información del Sistema</h3>

            <table class="widefat">
                <tr>
                    <td><strong>WordPress Version:</strong></td>
                    <td><?php echo get_bloginfo('version'); ?></td>
                </tr>
                <tr>
                    <td><strong>PHP Version:</strong></td>
                    <td><?php echo PHP_VERSION; ?></td>
                </tr>
                <tr>
                    <td><strong>GraphQL Plugin:</strong></td>
                    <td><?php echo class_exists('WPGraphQL') ? '✅ Instalado' : '❌ No instalado'; ?></td>
                </tr>
                <tr>
                    <td><strong>Headless Plugin Version:</strong></td>
                    <td>2.0</td>
                </tr>
                <tr>
                    <td><strong>Modo Debug:</strong></td>
                    <td><?php echo WP_DEBUG ? '✅ Activo' : '❌ Desactivado'; ?></td>
                </tr>
                <tr>
                    <td><strong>Frontend URL:</strong></td>
                    <td><code><?php echo home_url(); ?></code></td>
                </tr>
                <tr>
                    <td><strong>GraphQL URL:</strong></td>
                    <td><code><?php echo home_url('/graphql'); ?></code></td>
                </tr>
            </table>
        </div>
<?php
    }
}
