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
    private AssetManager $assetManager;

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

        // Inicializar asset manager
        $this->assetManager = new AssetManager(
            $this->headlessHandler,
            HEADLESS_WP_ADMIN_VERSION,
            HEADLESS_WP_ADMIN_PLUGIN_URL,
            HEADLESS_WP_ADMIN_PLUGIN_DIR
        );

        add_action('init', [$this, 'loadTextdomain']);

        // Inicializar componentes
        if (is_admin()) {
            new AdminPage($this->headlessHandler);
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
        if (!current_user_can('manage_options')) {
            return;
        }

        $screen = get_current_screen();
        if ($screen instanceof WP_Screen && $screen->id !== 'toplevel_page_headless-mode') {
            return;
        }

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
            'href' => admin_url('admin.php?page=headless-mode')
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
                <a href="' . admin_url('admin.php?page=headless-mode') . '" class="button button-primary">Configurar</a>
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

    /**
     * Obtener el AssetManager instance
     */
    public function get_asset_manager(): AssetManager
    {
        return $this->assetManager;
    }

    /**
     * Obtener el HeadlessHandler instance
     */
    public function get_headless_handler(): HeadlessHandler
    {
        return $this->headlessHandler;
    }

    public static function activate(): void
    {
        // Código de activación
        flush_rewrite_rules();

        // Crear opciones por defecto si no existen
        $headlessHandler = new HeadlessHandler();
        $default_settings = $headlessHandler->get_settings();

        if (!get_option('headless_wp_settings')) {
            update_option('headless_wp_settings', $default_settings);
        }
    }

    public static function deactivate(): void
    {
        // Código de desactivación
        flush_rewrite_rules();

        // Limpiar cron jobs si es necesario
        $timestamp = wp_next_scheduled('headless_cleanup_event');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'headless_cleanup_event');
        }
    }

    public static function uninstall(): void
    {
        // Código de desinstalación
        delete_option('headless_wp_settings');
        delete_option('headless_wp_cache');

        // Limpiar cualquier cache relacionado
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }

        // Limpiar transients si es necesario
        delete_transient('headless_api_status');
        delete_transient('headless_settings_hash');
    }
}
