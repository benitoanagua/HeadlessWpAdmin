<?php

namespace HeadlessWPAdmin\Core;

/**
 * Clase principal del plugin
 */
class Plugin {
    
    private static ?$instance = null;
    
    private function __construct() {
        $this->init();
    }
    
    public static function getInstance(): self {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function init(): void {
        add_action('init', [$this, 'loadTextdomain']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueFrontendAssets']);
        
        // Inicializar componentes
        if (is_admin()) {
            new Admin\AdminPage();
        } else {
            new Frontend\PublicInterface();
        }
    }
    
    public function loadTextdomain(): void {
        load_plugin_textdomain(
            'headless-wp-admin',
            false,
            dirname(HEADLESS-WP-ADMIN_PLUGIN_BASENAME) . '/languages/'
        );
    }
    
    public function enqueueAdminAssets(): void {
        wp_enqueue_script(
            'headless-wp-admin-admin',
            HEADLESS-WP-ADMIN_PLUGIN_URL . 'public/js/admin.js',
            ['wp-api-fetch'],
            HEADLESS-WP-ADMIN_VERSION,
            true
        );
        
        wp_enqueue_style(
            'headless-wp-admin-admin',
            HEADLESS-WP-ADMIN_PLUGIN_URL . 'public/css/admin.css',
            [],
            HEADLESS-WP-ADMIN_VERSION
        );
    }
    
    public function enqueueFrontendAssets(): void {
        wp_enqueue_script(
            'headless-wp-admin-frontend',
            HEADLESS-WP-ADMIN_PLUGIN_URL . 'public/js/frontend.js',
            [],
            HEADLESS-WP-ADMIN_VERSION,
            true
        );
        
        wp_enqueue_style(
            'headless-wp-admin-frontend',
            HEADLESS-WP-ADMIN_PLUGIN_URL . 'public/css/frontend.css',
            [],
            HEADLESS-WP-ADMIN_VERSION
        );
    }
    
    public static function activate(): void {
        // Código de activación
        flush_rewrite_rules();
    }
    
    public static function deactivate(): void {
        // Código de desactivación
        flush_rewrite_rules();
    }
    
    public static function uninstall(): void {
        // Código de desinstalación
        // Limpiar opciones, tablas, etc.
    }
}
