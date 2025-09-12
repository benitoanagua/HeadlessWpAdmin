<?php

namespace HeadlessWPAdmin\Admin\Tabs;

class SecurityTab
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
            <h3>🔒 Configuración de Seguridad</h3>

            <div class="headless-grid">
                <div class="headless-form-row">
                    <label>
                        <input type="checkbox" name="security_headers_enabled" value="1" <?php checked($settings['security_headers_enabled']); ?>>
                        Habilitar headers de seguridad
                    </label>
                    <p class="description">Agrega X-Content-Type-Options, X-Frame-Options, etc.</p>
                </div>

                <div class="headless-form-row">
                    <label>
                        <input type="checkbox" name="block_theme_access" value="1" <?php checked($settings['block_theme_access']); ?>>
                        Bloquear acceso directo a archivos de tema
                    </label>
                    <p class="description">Previene acceso directo a archivos PHP del tema</p>
                </div>

                <div class="headless-form-row">
                    <label>
                        <input type="checkbox" name="rate_limiting_enabled" value="1" <?php checked($settings['rate_limiting_enabled']); ?>>
                        Habilitar rate limiting básico
                    </label>
                    <p class="description">Limitación básica de requests por IP (experimental)</p>
                </div>

                <div class="headless-form-row">
                    <label>
                        <input type="checkbox" name="debug_logging" value="1" <?php checked($settings['debug_logging']); ?>>
                        Habilitar logging de debug
                    </label>
                    <p class="description">Registra intentos de acceso bloqueados en error.log</p>
                </div>
            </div>
        </div>

        <div class="headless-config-section">
            <h3>📊 Estadísticas de Bloqueos (Últimas 24h)</h3>
            <div id="security-stats">
                <p><em>Función en desarrollo - Próximamente estadísticas de accesos bloqueados</em></p>
            </div>
        </div>
<?php
    }
}
