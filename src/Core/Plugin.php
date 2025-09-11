<?php

namespace HeadlessWPAdmin\Core;

use HeadlessWPAdmin\Admin\AdminPage;
use HeadlessWPAdmin\Frontend\PublicInterface;
use HeadlessWPAdmin\API\SettingsEndpoint;
use WP_Admin_Bar;
use WP_Screen;

/**
 * Clase principal del plugin
 */
class Plugin
{
    private static ?self $instance = null;
    private HeadlessHandler $headlessHandler;

    private function __construct()
    {
        $this->init();
    }

    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function init(): void
    {
        // Inicializar el manejador headless
        $this->headlessHandler = new HeadlessHandler();

        add_action('init', [$this, 'loadTextdomain']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueFrontendAssets']);

        // Inicializar componentes
        if (is_admin()) {
            new AdminPage();
        } else {
            new PublicInterface();
        }

        // Inicializar API endpoints
        new SettingsEndpoint();

        // Configurar interfaz de administración
        $this->configure_admin_interface();
    }

    private function configure_admin_interface(): void
    {
        add_action('admin_notices', [$this, 'admin_notices']);
        add_action('admin_bar_menu', [$this, 'modify_admin_bar'], 999);
        add_action('wp_dashboard_setup', [$this, 'add_dashboard_widgets']);
    }

    public function admin_notices(): void
    {
        if (!current_user_can('manage_options')) return;

        $screen = get_current_screen();
        if ($screen instanceof WP_Screen && $screen->id !== 'settings_page_headless-mode') return;

        echo '<div class="notice notice-info">
            <h3>🚀 Headless WordPress Activo</h3>
            <p>Configuración completamente personalizable desde esta página.</p>
        </div>';
    }

    public function modify_admin_bar(WP_Admin_Bar $wp_admin_bar): void
    {
        $wp_admin_bar->remove_node('view-site');

        if ($this->headlessHandler->get_setting('graphql_enabled')) {
            $wp_admin_bar->add_node([
                'id' => 'graphql-endpoint',
                'title' => '⚡ GraphQL',
                'href' => home_url('/graphql'),
                'meta' => ['target' => '_blank']
            ]);
        }

        $wp_admin_bar->add_node([
            'id' => 'headless-config',
            'title' => '🚀 Headless',
            'href' => admin_url('options-general.php?page=headless-mode')
        ]);
    }

    public function add_dashboard_widgets(): void
    {
        wp_add_dashboard_widget(
            'headless_status',
            '🚀 Estado Headless',
            [$this, 'dashboard_widget']
        );
    }

    public function dashboard_widget(): void
    {
        $settings = $this->headlessHandler->get_settings();

        echo '<div style="text-align: center;">
            <p><strong>Configuración Headless Activa</strong></p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 15px 0;">
                <div>GraphQL: ' . ($settings['graphql_enabled'] ? '✅' : '❌') . '</div>
                <div>REST API: ' . ($settings['rest_api_enabled'] ? '✅' : '❌') . '</div>
                <div>Frontend: ❌ Bloqueado</div>
                <div>Admin: ✅ Activo</div>
            </div>
            <p>
                <a href="' . admin_url('options-general.php?page=headless-mode') . '" class="button button-primary">Configurar</a>
                <a href="' . home_url('/') . '" target="_blank" class="button">Ver Página Bloqueada</a>
            </p>
        </div>';
    }

    public function loadTextdomain(): void
    {
        load_plugin_textdomain(
            'headless-wp-admin',
            false,
            dirname(plugin_basename(HEADLESS_WP_ADMIN_PLUGIN_FILE)) . '/languages/'
        );
    }

    public function enqueueAdminAssets(): void
    {
        wp_enqueue_script(
            'headless-wp-admin-admin',
            HEADLESS_WP_ADMIN_PLUGIN_URL . 'public/js/admin.js',
            ['wp-api-fetch'],
            HEADLESS_WP_ADMIN_VERSION,
            true
        );

        wp_enqueue_style(
            'headless-wp-admin-admin',
            HEADLESS_WP_ADMIN_PLUGIN_URL . 'public/css/admin.css',
            [],
            HEADLESS_WP_ADMIN_VERSION
        );
    }

    public function enqueueFrontendAssets(): void
    {
        wp_enqueue_script(
            'headless-wp-admin-frontend',
            HEADLESS_WP_ADMIN_PLUGIN_URL . 'public/js/frontend.js',
            [],
            HEADLESS_WP_ADMIN_VERSION,
            true
        );

        wp_enqueue_style(
            'headless-wp-admin-frontend',
            HEADLESS_WP_ADMIN_PLUGIN_URL . 'public/css/frontend.css',
            [],
            HEADLESS_WP_ADMIN_VERSION
        );
    }

    public static function activate(): void
    {
        // Código de activación
        flush_rewrite_rules();
    }

    public static function deactivate(): void
    {
        // Código de desactivación
        flush_rewrite_rules();
    }

    public static function uninstall(): void
    {
        // Código de desinstalación
        delete_option('headless_wp_settings');

        // Limpiar cualquier cache relacionado
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
    }
}
