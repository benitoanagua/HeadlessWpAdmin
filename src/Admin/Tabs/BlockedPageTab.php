<?php

namespace HeadlessWPAdmin\Admin\Tabs;

class BlockedPageTab
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
            <h3>🎨 Apariencia de la Página</h3>

            <div class="headless-grid">
                <div class="headless-form-row">
                    <label for="blocked_page_title">Título Principal</label>
                    <input type="text" name="blocked_page_title" id="blocked_page_title" value="<?php echo esc_attr($settings['blocked_page_title']); ?>">
                </div>

                <div class="headless-form-row">
                    <label for="blocked_page_subtitle">Subtítulo</label>
                    <input type="text" name="blocked_page_subtitle" id="blocked_page_subtitle" value="<?php echo esc_attr($settings['blocked_page_subtitle']); ?>">
                </div>
            </div>

            <div class="headless-form-row">
                <label for="blocked_page_message">Mensaje Principal</label>
                <textarea name="blocked_page_message" id="blocked_page_message" rows="4"><?php echo esc_textarea($settings['blocked_page_message']); ?></textarea>
                <p class="description">Puedes usar HTML básico. Deja vacío para no mostrar mensaje.</p>
            </div>

            <div class="headless-grid">
                <div class="headless-form-row">
                    <label for="blocked_page_icon">Icono/Emoji</label>
                    <input type="text" name="blocked_page_icon" id="blocked_page_icon" value="<?php echo esc_attr($settings['blocked_page_icon']); ?>" maxlength="10">
                    <p class="description">Emoji o símbolo que se mostrará en grande</p>
                </div>

                <div class="headless-form-row">
                    <label for="blocked_page_logo_url">URL del Logo</label>
                    <input type="url" name="blocked_page_logo_url" id="blocked_page_logo_url" value="<?php echo esc_url($settings['blocked_page_logo_url']); ?>">
                    <p class="description">URL completa de la imagen del logo</p>
                </div>
            </div>
        </div>

        <div class="headless-config-section">
            <h3>🎨 Colores y Diseño</h3>

            <div class="headless-grid">
                <div class="headless-form-row">
                    <label for="blocked_page_background_color">Color de Fondo Base</label>
                    <input type="text" name="blocked_page_background_color" id="blocked_page_background_color" value="<?php echo esc_attr($settings['blocked_page_background_color']); ?>" class="color-picker">
                </div>

                <div class="headless-form-row">
                    <label for="blocked_page_background_gradient">Gradiente de Fondo (CSS)</label>
                    <input type="text" name="blocked_page_background_gradient" id="blocked_page_background_gradient" value="<?php echo esc_attr($settings['blocked_page_background_gradient']); ?>">
                    <p class="description">Ej: linear-gradient(135deg, #667eea 0%, #764ba2 100%)</p>
                </div>
            </div>

            <div class="headless-form-row">
                <label for="blocked_page_custom_css">CSS Personalizado</label>
                <textarea name="blocked_page_custom_css" id="blocked_page_custom_css" rows="8"><?php echo esc_textarea($settings['blocked_page_custom_css']); ?></textarea>
                <p class="description">CSS adicional para personalizar completamente la página</p>
            </div>
        </div>

        <div class="headless-config-section">
            <h3>📝 Contenido enlaces</h3>

            <div class="headless-grid">
                <div class="headless-form-row">
                    <label>
                        <input type="checkbox" name="blocked_page_show_admin_link" value="1" <?php checked($settings['blocked_page_show_admin_link']); ?>>
                        Mostrar enlace a administración
                    </label>
                </div>

                <div class="headless-form-row">
                    <label>
                        <input type="checkbox" name="blocked_page_show_graphql_link" value="1" <?php checked($settings['blocked_page_show_graphql_link']); ?>>
                        Mostrar enlace a GraphQL
                    </label>
                </div>

                <div class="headless-form-row">
                    <label>
                        <input type="checkbox" name="blocked_page_show_status_info" value="1" <?php checked($settings['blocked_page_show_status_info']); ?>>
                        Mostrar información de estado de APIs
                    </label>
                </div>
            </div>

            <div class="headless-form-row">
                <label for="blocked_page_contact_info">Información de Contacto</label>
                <textarea name="blocked_page_contact_info" id="blocked_page_contact_info" rows="3"><?php echo esc_textarea($settings['blocked_page_contact_info']); ?></textarea>
                <p class="description">Información adicional que aparecerá al final de la página (HTML permitido)</p>
            </div>
        </div>
<?php
    }
}
