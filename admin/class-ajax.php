<?php
namespace HeadlessWPAdmin\Admin;

class Ajax {
    private $option_name = 'headless_wp_settings';

    public function __construct() {
        add_action('wp_ajax_headless_test_endpoint', array($this, 'test_endpoint'));
        add_action('wp_ajax_headless_reset_settings', array($this, 'reset_settings'));
        add_action('wp_ajax_headless_export_settings', array($this, 'export_settings'));
        add_action('wp_ajax_headless_import_settings', array($this, 'import_settings'));
    }

    public function test_endpoint() {
        check_ajax_referer('headless_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('No permissions');
        }

        $endpoint = $_POST['endpoint'] ?? '';
        
        if ($endpoint === 'graphql') {
            $response = wp_remote_get(home_url('/graphql'));
            if (!is_wp_error($response)) {
                wp_send_json_success('GraphQL endpoint responds correctly');
            } else {
                wp_send_json_error('Error connecting to GraphQL: ' . $response->get_error_message());
            }
        } elseif ($endpoint === 'rest') {
            $response = wp_remote_get(home_url('/wp-json/'));
            if (!is_wp_error($response)) {
                wp_send_json_success('REST API endpoint responds correctly');
            } else {
                wp_send_json_error('Error connecting to REST API: ' . $response->get_error_message());
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
        wp_send_json_success('Settings reset successfully');
    }

    public function export_settings() {
        check_ajax_referer('headless_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('No permissions');
        }

        $settings = get_option($this->option_name, array());
        $export_data = json_encode($settings, JSON_PRETTY_PRINT);
        
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="headless-settings-' . date('Y-m-d') . '.json"');
        header('Content-Length: ' . strlen($export_data));
        
        echo $export_data;
        exit;
    }

    public function import_settings() {
        check_ajax_referer('headless_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('No permissions');
        }

        if (empty($_FILES['import_file'])) {
            wp_send_json_error('No file uploaded');
        }

        $file = $_FILES['import_file'];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error('File upload error: ' . $file['error']);
        }

        if ($file['type'] !== 'application/json') {
            wp_send_json_error('Invalid file type. Please upload a JSON file.');
        }

        $file_content = file_get_contents($file['tmp_name']);
        $settings = json_decode($file_content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error('Invalid JSON format: ' . json_last_error_msg());
        }

        update_option($this->option_name, $settings);
        wp_send_json_success('Settings imported successfully');
    }
}