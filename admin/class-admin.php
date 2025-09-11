<?php
namespace HeadlessWPAdmin\Admin;

class Admin {
    private $plugin_name;
    private $version;
    private $option_name = 'headless_wp_settings';

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles($hook) {
        if (strpos($hook, 'headless-mode') !== false) {
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_style($this->plugin_name, HEADLESS_WP_ADMIN_PLUGIN_URL . 'admin/css/admin.css', array(), $this->version, 'all');
        }
    }

    public function enqueue_scripts($hook) {
        if (strpos($hook, 'headless-mode') !== false) {
            wp_enqueue_script('wp-color-picker');
            wp_enqueue_script($this->plugin_name, HEADLESS_WP_ADMIN_PLUGIN_URL . 'admin/js/admin.js', array('jquery'), $this->version, false);
            
            wp_localize_script($this->plugin_name, 'headless_admin', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('headless_admin_nonce')
            ));
        }
    }

    public function add_admin_menu() {
        add_options_page(
            'Headless WordPress',
            'Headless Mode',
            'manage_options',
            'headless-mode',
            array($this, 'admin_page')
        );
    }

    public function settings_init() {
        register_setting('headless_wp_settings', $this->option_name);
    }

    public function admin_notices() {
        if (!current_user_can('manage_options')) return;

        $screen = get_current_screen();
        if ($screen->id !== 'settings_page_headless-mode') return;

        echo '<div class="notice notice-info">
            <h3>🚀 Headless WordPress Active</h3>
            <p>Fully configurable settings from this page.</p>
        </div>';

        $settings = get_option($this->option_name, array());
        if (!empty($settings['graphql_enabled']) && !class_exists('WPGraphQL')) {
            echo '<div class="notice notice-warning">
                <p><strong>Headless WordPress:</strong> GraphQL is enabled but WPGraphQL plugin is not installed. 
                <a href="' . admin_url('plugin-install.php?s=wpgraphql&tab=search&type=term') . '">Install WPGraphQL</a></p>
            </div>';
        }
    }

    public function admin_page() {
        if (isset($_POST['submit'])) {
            $this->save_settings();
        }

        $settings = get_option($this->option_name, array());
        $active_tab = $_GET['tab'] ?? 'general';

        include HEADLESS_WP_ADMIN_PLUGIN_PATH . 'admin/partials/admin-display.php';
    }

    private function save_settings() {
        if (!wp_verify_nonce($_POST['headless_nonce'], 'headless_settings_save')) {
            wp_die('Security error');
        }

        if (!current_user_can('manage_options')) {
            wp_die('No permissions');
        }

        $new_settings = array();
        $default_settings = (new \HeadlessWPAdmin\Activator)::activate();
        
        foreach ($default_settings as $key => $default_value) {
            if (isset($_POST[$key])) {
                $value = $_POST[$key];
                
                if (is_bool($default_value)) {
                    $new_settings[$key] = !empty($value);
                } elseif (filter_var($default_value, FILTER_VALIDATE_URL)) {
                    $new_settings[$key] = esc_url_raw($value);
                } elseif (in_array($key, ['blocked_page_custom_css', 'custom_redirect_rules', 'custom_headers'])) {
                    $new_settings[$key] = wp_unslash($value);
                } else {
                    $new_settings[$key] = sanitize_textarea_field($value);
                }
            } else {
                if (is_bool($default_value)) {
                    $new_settings[$key] = false;
                }
            }
        }

        update_option($this->option_name, $new_settings);
        
        echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
    }

    public function test_endpoint() {
        check_ajax_referer('headless_admin_nonce', 'nonce');
        
        $endpoint = $_POST['endpoint'] ?? '';
        
        if ($endpoint === 'graphql') {
            $response = wp_remote_get(home_url('/graphql'));
            if (!is_wp_error($response)) {
                wp_send_json_success('GraphQL endpoint responds correctly');
            } else {
                wp_send_json_error('Error connecting to GraphQL: ' . $response->get_error_message());
            }
        }
        
        wp_send_json_error('Invalid endpoint');
    }

    public function reset_settings() {
        check_ajax_referer('headless_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('No permissions');
        }

        delete_option($this->option_name);
        wp_send_json_success('Settings reset');
    }
}