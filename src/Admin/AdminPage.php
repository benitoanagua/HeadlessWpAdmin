<?php

namespace HeadlessWPAdmin\Admin;

use HeadlessWPAdmin\Admin\Tabs\GeneralTab;
use HeadlessWPAdmin\Admin\Tabs\APIsTab;
use HeadlessWPAdmin\Admin\Tabs\BlockedPageTab;
use HeadlessWPAdmin\Admin\Tabs\SecurityTab;
use HeadlessWPAdmin\Admin\Tabs\AdvancedTab;
use HeadlessWPAdmin\Core\HeadlessHandler;

/**
 * Clase para manejar la página de administración
 */
class AdminPage
{
    private HeadlessHandler $headlessHandler;
    private GeneralTab $generalTab;
    private APIsTab $apisTab;
    private BlockedPageTab $blockedPageTab;
    private SecurityTab $securityTab;
    private AdvancedTab $advancedTab;

    public function __construct()
    {
        $this->headlessHandler = new HeadlessHandler();
        $this->generalTab = new GeneralTab();
        $this->apisTab = new APIsTab();
        $this->blockedPageTab = new BlockedPageTab();
        $this->securityTab = new SecurityTab();
        $this->advancedTab = new AdvancedTab();

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
                        $this->generalTab->render($settings);
                        break;
                    case 'apis':
                        $this->apisTab->render($settings);
                        break;
                    case 'blocked-page':
                        $this->blockedPageTab->render($settings);
                        break;
                    case 'security':
                        $this->securityTab->render($settings);
                        break;
                    case 'advanced':
                        $this->advancedTab->render($settings);
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

    <?php
        $this->renderStyles();
        $this->renderScripts();
    }

    public function admin_init(): void
    {
        register_setting('headless_wp_settings', 'headless_wp_settings');
    }

    private function renderStyles(): void
    {
    ?>
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
    <?php
    }

    private function renderScripts(): void
    {
    ?>
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
