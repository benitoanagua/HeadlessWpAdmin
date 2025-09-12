<?php

namespace HeadlessWPAdmin\Admin\Tabs;

class GeneralTab
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
            <h3>📋 Configuración General</h3>

            <div class="headless-form-row">
                <label>
                    <input type="checkbox" name="blocked_page_enabled" value="1" <?php checked($settings['blocked_page_enabled']); ?>>
                    Mostrar página personalizada cuando se bloquee el acceso
                </label>
                <p class="description">Si está deshabilitado, se mostrará un error 403 estándar.</p>
            </div>

            <div class="headless-form-row">
                <label for="allowed_paths">Rutas Permitidas</label>
                <textarea name="allowed_paths" id="allowed_paths" rows="6"><?php echo esc_textarea($settings['allowed_paths']); ?></textarea>
                <p class="description">Una ruta por línea. Estas rutas no serán bloqueadas por el sistema headless.</p>
            </div>

            <div class="headless-grid">
                <div class="headless-form-row">
                    <label>
                        <input type="checkbox" name="media_access_enabled" value="1" <?php checked($settings['media_access_enabled']); ?>>
                        Permitir acceso directo a archivos multimedia
                    </label>
                    <p class="description">Permite acceso a /wp-content/uploads/</p>
                </div>

                <div class="headless-form-row">
                    <label>
                        <input type="checkbox" name="preview_access_enabled" value="1" <?php checked($settings['preview_access_enabled']); ?>>
                        Permitir preview para usuarios autenticados
                    </label>
                    <p class="description">Los editores pueden ver previsualizaciones del contenido.</p>
                </div>
            </div>
        </div>

        <div class="headless-config-section">
            <h3>🧹 Limpieza de WordPress</h3>
            <div class="headless-grid">
                <div class="headless-form-row">
                    <label>
                        <input type="checkbox" name="disable_feeds" value="1" <?php checked($settings['disable_feeds']); ?>>
                        Deshabilitar feeds RSS
                    </label>
                </div>

                <div class="headless-form-row">
                    <label>
                        <input type="checkbox" name="disable_sitemaps" value="1" <?php checked($settings['disable_sitemaps']); ?>>
                        Deshabilitar XML sitemaps
                    </label>
                </div>

                <div class="headless-form-row">
                    <label>
                        <input type="checkbox" name="disable_comments" value="1" <?php checked($settings['disable_comments']); ?>>
                        Deshabilitar comentarios
                    </label>
                </div>

                <div class="headless-form-row">
                    <label>
                        <input type="checkbox" name="disable_embeds" value="1" <?php checked($settings['disable_embeds']); ?>>
                        Deshabilitar oEmbed
                    </label>
                </div>

                <div class="headless-form-row">
                    <label>
                        <input type="checkbox" name="disable_emojis" value="1" <?php checked($settings['disable_emojis']); ?>>
                        Deshabilitar emojis
                    </label>
                </div>

                <div class="headless-form-row">
                    <label>
                        <input type="checkbox" name="clean_wp_head" value="1" <?php checked($settings['clean_wp_head']); ?>>
                        Limpiar wp_head()
                    </label>
                </div>
            </div>
        </div>
<?php
    }
}
