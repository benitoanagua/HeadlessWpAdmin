<?php

namespace HeadlessWPAdmin\Core;

/**
 * Gestión centralizada de assets CSS y JS
 */
class AssetManager
{
    private HeadlessHandler $headlessHandler;
    private string $version;
    private string $plugin_url;
    private string $plugin_path;

    public function __construct(HeadlessHandler $headlessHandler, string $version, string $plugin_url, string $plugin_path)
    {
        $this->headlessHandler = $headlessHandler;
        $this->version = $version;
        $this->plugin_url = $plugin_url;
        $this->plugin_path = $plugin_path;

        $this->init();
    }

    private function init(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
        add_action('admin_head', [$this, 'add_admin_dynamic_styles']);
        add_action('wp_head', [$this, 'add_frontend_dynamic_styles'], 5);
    }

    /**
     * Cargar assets del admin
     */
    public function enqueue_admin_assets(string $hook): void
    {
        // Solo cargar en páginas del plugin
        if (!$this->is_plugin_admin_page($hook)) {
            return;
        }

        // CSS del admin
        $this->enqueue_admin_styles();

        // JS del admin
        $this->enqueue_admin_scripts();

        // Assets de WordPress necesarios
        $this->enqueue_wordpress_assets();
    }

    /**
     * Cargar assets del frontend
     */
    public function enqueue_frontend_assets(): void
    {
        // Solo cargar en páginas bloqueadas
        if (!$this->is_blocked_page_context()) {
            return;
        }

        $this->enqueue_blocked_page_styles();
    }

    /**
     * Encolar estilos del admin
     */
    private function enqueue_admin_styles(): void
    {
        // Estilo principal del admin
        wp_enqueue_style(
            'headless-admin-main',
            $this->plugin_url . 'assets/css/admin/main.css',
            [],
            $this->version
        );

        // Componentes del admin
        wp_enqueue_style(
            'headless-admin-components',
            $this->plugin_url . 'assets/css/admin/components.css',
            ['headless-admin-main'],
            $this->version
        );

        // Formularios
        wp_enqueue_style(
            'headless-admin-forms',
            $this->plugin_url . 'assets/css/admin/forms.css',
            ['headless-admin-main'],
            $this->version
        );
    }

    /**
     * Encolar scripts del admin
     */
    private function enqueue_admin_scripts(): void
    {
        wp_enqueue_script(
            'headless-admin-main',
            $this->plugin_url . 'assets/js/admin/main.js',
            ['jquery', 'wp-color-picker'],
            $this->version,
            true
        );

        // Localizar script con configuración
        wp_localize_script('headless-admin-main', 'headlessAdmin', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('headless_admin_nonce'),
            'homeUrl' => home_url(),
            'graphqlUrl' => home_url('/graphql'),
            'restUrl' => home_url('/wp-json/'),
            'strings' => [
                'confirmReset' => __('¿Resetear toda la configuración?', 'headless-wp-admin'),
                'graphqlTestSuccess' => __('GraphQL responde correctamente', 'headless-wp-admin'),
                'graphqlTestError' => __('Error al conectar con GraphQL', 'headless-wp-admin'),
            ]
        ]);
    }

    /**
     * Encolar assets de WordPress necesarios
     */
    private function enqueue_wordpress_assets(): void
    {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }

    /**
     * Encolar estilos para páginas bloqueadas
     */
    private function enqueue_blocked_page_styles(): void
    {
        $settings = $this->headlessHandler->get_settings();
        $css_hash = $this->generate_css_hash($settings);

        // Estilo base de página bloqueada
        wp_enqueue_style(
            'headless-blocked-page',
            $this->plugin_url . 'assets/css/frontend/blocked-page.css',
            [],
            $this->version . '-' . $css_hash
        );

        // CSS personalizado inline
        $custom_css = $this->generate_blocked_page_custom_css($settings);
        if (!empty($custom_css)) {
            wp_add_inline_style('headless-blocked-page', $custom_css);
        }
    }

    /**
     * Agregar estilos dinámicos en el admin
     */
    public function add_admin_dynamic_styles(): void
    {
        if (!$this->is_plugin_admin_page()) {
            return;
        }

        echo '<style id="headless-admin-dynamic">
        .headless-config-section {
            --headless-primary: #007cba;
            --headless-success: #46b450;
            --headless-warning: #ffb900;
            --headless-error: #dc3232;
            --headless-border-radius: 8px;
            --headless-box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        </style>';
    }

    /**
     * Agregar estilos dinámicos en el frontend
     */
    public function add_frontend_dynamic_styles(): void
    {
        if (!$this->is_blocked_page_context()) {
            return;
        }

        $settings = $this->headlessHandler->get_settings();

        echo '<style id="headless-dynamic-vars">
        :root {
            --headless-primary-color: ' . esc_attr($settings['blocked_page_background_color'] ?? '#667eea') . ';
            --headless-gradient: ' . esc_attr($settings['blocked_page_background_gradient'] ?? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)') . ';
            --headless-icon-size: 80px;
            --headless-border-radius: 20px;
            --headless-container-bg: rgba(255, 255, 255, 0.95);
        }
        </style>';
    }

    /**
     * Generar CSS personalizado para página bloqueada
     * 
     * @param array<string, mixed> $settings
     */
    private function generate_blocked_page_custom_css(array $settings): string
    {
        $custom_css = $settings['blocked_page_custom_css'] ?? '';

        if (!empty($custom_css)) {
            // Sanitizar CSS
            $custom_css = wp_strip_all_tags($custom_css);
            $custom_css = preg_replace('/[\\0-\\x1F\\x7F]/', '', $custom_css);
            return '/* Custom CSS */ ' . $custom_css;
        }

        return '';
    }

    /**
     * Generar hash para cache busting
     * 
     * @param array<string, mixed> $settings
     */
    private function generate_css_hash(array $settings): string
    {
        $css_settings = [
            $settings['blocked_page_background_color'] ?? '',
            $settings['blocked_page_background_gradient'] ?? '',
            $settings['blocked_page_custom_css'] ?? ''
        ];

        return substr(md5(implode('|', $css_settings)), 0, 8);
    }

    /**
     * Verificar si estamos en página de admin del plugin
     */
    private function is_plugin_admin_page(string $hook = ''): bool
    {
        global $pagenow;

        $current_hook = $hook ?: ($pagenow ?? '');
        $page = $_GET['page'] ?? '';

        // Lógica corregida
        $is_headless_hook = strpos($current_hook, 'headless-') === 0;
        $is_headless_page = strpos($page, 'headless-') === 0;
        $is_admin_page_with_headless = $current_hook === 'admin.php' && strpos($page, 'headless-') === 0;

        return $is_headless_hook || $is_headless_page || $is_admin_page_with_headless;
    }

    /**
     * Verificar si estamos en contexto de página bloqueada
     */
    private function is_blocked_page_context(): bool
    {
        return !is_admin() &&
            !wp_doing_ajax() &&
            !wp_doing_cron() &&
            !$this->headlessHandler->is_allowed_request();
    }

    /**
     * Obtener CSS para página bloqueada (para uso en template)
     */
    public function get_blocked_page_css(): string
    {
        $css_file = $this->plugin_path . 'public/css/frontend/blocked-page.css';
        $base_css = file_exists($css_file) ? file_get_contents($css_file) : '';

        $settings = $this->headlessHandler->get_settings();
        $custom_css = $this->generate_blocked_page_custom_css($settings);

        return $base_css . $custom_css;
    }
}
