<?php

namespace HeadlessWPAdmin\Admin;

/**
 * Clase para manejar la página de administración
 */
class AdminPage {
    
    public function __construct() {
        add_action('admin_menu', [$this, 'addAdminMenu']);
    }
    
    public function addAdminMenu(): void {
        add_menu_page(
            __('Headless WordPress Admin', 'headless-wp-admin'),
            __('Headless WordPress Admin', 'headless-wp-admin'),
            'manage_options',
            'headless-wp-admin',
            [$this, 'renderAdminPage'],
            'dashicons-admin-generic',
            30
        );
    }
    
    public function renderAdminPage(): void {
        echo '<div id="headless-wp-admin-admin-app"></div>';
    }
}
