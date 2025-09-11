<?php

namespace HeadlessWPAdmin\Admin;

use HeadlessWPAdmin\Core\HeadlessHandler;

/**
 * Clase para manejar la página de administración
 */
class AdminPage
{

    private HeadlessHandler $headlessHandler;

    public function __construct()
    {
        $this->headlessHandler = new HeadlessHandler();
        add_action('admin_menu', [$this, 'addAdminMenu']);
        add_action('admin_init', [$this, 'admin_init']);
        add_action('wp_ajax_headless_test_endpoint', [$this, 'test_endpoint']);
        add_action('wp_ajax_headless_reset_settings', [$this, 'reset_settings']);
    }

    public function addAdminMenu(): void
    {
        add_menu_page(
            __('Headless WordPress Admin', 'headless-wp-admin'),
            __('Headless WordPress Admin', 'headless-wp-admin'),
            'manage_options',
            'headless-wp-admin',
            [$this, 'renderAdminPage'],
            'dashicons-admin-generic',
            30
        );

        add_submenu_page(
            'headless-wp-admin',
            __('Configuración Headless', 'headless-wp-admin'),
            __('Configuración Headless', 'headless-wp-admin'),
            'manage_options',
            'headless-mode',
            [$this, 'renderHeadlessConfigPage']
        );
    }

    public function renderAdminPage(): void
    {
        echo '<div id="headless-wp-admin-admin-app"></div>';
    }

    public function renderHeadlessConfigPage(): void
    {
        if (isset($_POST['submit'])) {
            $this->save_settings();
        }

        $settings = $this->headlessHandler->get_settings();
        $active_tab = $_GET['tab'] ?? 'general';

?>
        <div class="wrap">
            <h1>🚀 Headless WordPress - Configuración</h1>

            <h2 class="nav-tab-wrapper">
                <a href="?page=headless-mode&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General</a>
                <a href="?page=headless-mode&tab=apis" class="nav-tab <?php echo $active_tab == 'apis' ? 'nav-tab-active' : ''; ?>">APIs</a>
                <a href="?page=headless-mode&tab=blocked-page" class="nav-tab <?php echo $active_tab == 'blocked-page' ? 'nav-tab-active' : ''; ?>">Página Bloqueada</a>
                <a href="?page=headless-mode&tab=security" class="nav-tab <?php echo $active_tab == 'security' ? 'nav-tab-active' : ''; ?>">Seguridad</a>
                <a href="?page=headless-mode&tab=advanced" class="nav-tab <?php echo $active_tab == 'advanced' ? 'nav-tab-active' : ''; ?>">Avanzado</a>
            </h2>

            <form method="post" action="">
                <?php wp_nonce_field('headless_settings_save', 'headless_nonce'); ?>

                <?php
                switch ($active_tab) {
                    case 'general':
                        $this->render_general_tab($settings);
                        break;
                    case 'apis':
                        $this->render_apis_tab($settings);
                        break;
                    case 'blocked-page':
                        $this->render_blocked_page_tab($settings);
                        break;
                    case 'security':
                        $this->render_security_tab($settings);
                        break;
                    case 'advanced':
                        $this->render_advanced_tab($settings);
                        break;
                }
                ?>

                <div class="headless-admin-footer">
                    <?php submit_button('Guardar Configuración', 'primary', 'submit', false); ?>
                    <button type="button" id="preview-blocked-page" class="button">👁️ Vista Previa</button>
                    <button type="button" id="reset-settings" class="button button-secondary" style="margin-left: 10px;">🔄 Resetear</button>
                </div>
            </form>
        </div>

        <style>
            .headless-config-section {
                background: #fff;
                border: 1px solid #ccd0d4;
                border-radius: 4px;
                margin: 20px 0;
                padding: 20px;
            }

            .headless-config-section h3 {
                margin-top: 0;
                border-bottom: 1px solid #eee;
                padding-bottom: 10px;
            }

            .headless-form-row {
                margin: 15px 0;
            }

            .headless-form-row label {
                display: block;
                font-weight: 600;
                margin-bottom: 5px;
            }

            .headless-form-row input[type="text"],
            .headless-form-row input[type="url"],
            .headless-form-row textarea,
            .headless-form-row select {
                width: 100%;
                max-width: 600px;
            }

            .headless-form-row textarea {
                height: 100px;
            }

            .headless-form-row .description {
                font-style: italic;
                color: #666;
                font-size: 13px;
            }

            .headless-admin-footer {
                background: #f1f1f1;
                padding: 20px;
                margin: 20px 0;
                border-radius: 4px;
            }

            .color-preview {
                display: inline-block;
                width: 30px;
                height: 30px;
                border-radius: 4px;
                border: 1px solid #ccc;
                margin-left: 10px;
                vertical-align: middle;
            }

            .headless-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
            }

            @media (max-width: 768px) {
                .headless-grid {
                    grid-template-columns: 1fr;
                }
            }

            .status-indicator {
                display: inline-block;
                width: 12px;
                height: 12px;
                border-radius: 50%;
                margin-right: 8px;
            }

            .status-active {
                background: #46b450;
            }

            .status-inactive {
                background: #dc3232;
            }
        </style>

        <script>
            jQuery(document).ready(function($) {
                $('.color-picker').wpColorPicker();

                $('#test-graphql').click(function() {
                    $.post(ajaxurl, {
                        action: 'headless_test_endpoint',
                        endpoint: 'graphql'
                    }, function(data) {
                        alert(data.success ? 'GraphQL OK' : 'GraphQL Error: ' + data.data);
                    });
                });

                $('#reset-settings').click(function() {
                    if (confirm('¿Resetear toda la configuración?')) {
                        $.post(ajaxurl, {
                            action: 'headless_reset_settings',
                            nonce: '<?php echo wp_create_nonce('headless_reset'); ?>'
                        }, function(data) {
                            if (data.success) location.reload();
                        });
                    }
                });

                $('#preview-blocked-page').click(function() {
                    window.open('<?php echo home_url(); ?>', '_blank');
                });
            });
        </script>
    <?php
    }

    public function admin_init(): void
    {
        register_setting('headless_wp_settings', 'headless_wp_settings');
    }

    /**
     * @param array<string, mixed> $settings
     */
    private function render_general_tab(array $settings): void
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

    /**
     * @param array<string, mixed> $settings
     */
    private function render_apis_tab(array $settings): void
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

    /**
     * @param array<string, mixed> $settings
     */
    private function render_blocked_page_tab(array $settings): void
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
            <h3>📝 Contenido y Enlaces</h3>

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

    /**
     * @param array<string, mixed> $settings
     */
    private function render_security_tab(array $settings): void
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

    /**
     * @param array<string, mixed> $settings
     */
    private function render_advanced_tab(array $settings): void
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

    private function save_settings(): void
    {
        if (!wp_verify_nonce($_POST['headless_nonce'], 'headless_settings_save')) {
            wp_die('Error de seguridad');
        }

        if (!current_user_can('manage_options')) {
            wp_die('Sin permisos');
        }

        $new_settings = [];
        $default_settings = $this->headlessHandler->get_settings();

        // Procesar todos los campos del formulario
        foreach ($default_settings as $key => $default_value) {
            if (isset($_POST[$key])) {
                $value = $_POST[$key];

                // Sanitizar según el tipo de campo
                if (is_bool($default_value)) {
                    $new_settings[$key] = !empty($value);
                } elseif (filter_var($default_value, FILTER_VALIDATE_URL)) {
                    $new_settings[$key] = esc_url_raw($value);
                } elseif (in_array($key, ['blocked_page_custom_css', 'custom_redirect_rules', 'custom_headers'])) {
                    $new_settings[$key] = wp_unslash($value); // Permitir CSS/código
                } else {
                    $new_settings[$key] = sanitize_textarea_field($value);
                }
            } else {
                // Para checkboxes no marcados
                if (is_bool($default_value)) {
                    $new_settings[$key] = false;
                }
            }
        }

        update_option('headless_wp_settings', $new_settings);

        echo '<div class="notice notice-success"><p>¡Configuración guardada correctamente!</p></div>';
    }

    public function test_endpoint(): void
    {
        $endpoint = $_POST['endpoint'] ?? '';

        if ($endpoint === 'graphql') {
            $response = wp_remote_get(home_url('/graphql'));
            if (!is_wp_error($response)) {
                wp_send_json_success('GraphQL endpoint responde correctamente');
            } else {
                wp_send_json_error('Error al conectar con GraphQL: ' . $response->get_error_message());
            }
        }

        wp_send_json_error('Endpoint no válido');
    }

    public function reset_settings(): void
    {
        if (!wp_verify_nonce($_POST['nonce'], 'headless_reset')) {
            wp_send_json_error('Nonce inválido');
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Sin permisos');
        }

        delete_option('headless_wp_settings');
        wp_send_json_success('Configuración reseteada');
    }
}
