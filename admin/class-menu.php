<?php
namespace HeadlessWPAdmin\Admin;

class Menu {
    public function add_admin_bar_items($wp_admin_bar) {
        if (!current_user_can('manage_options')) {
            return;
        }

        $wp_admin_bar->remove_node('view-site');

        $settings = get_option('headless_wp_settings', array());
        if (!empty($settings['graphql_enabled'])) {
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

    public function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'headless_status',
            '🚀 Headless Status',
            array($this, 'dashboard_widget_content')
        );
    }

    public function dashboard_widget_content() {
        $settings = get_option('headless_wp_settings', array());
        
        echo '<div style="text-align: center;">
            <p><strong>Headless Configuration Active</strong></p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 15px 0;">
                <div>GraphQL: ' . (!empty($settings['graphql_enabled']) ? '✅' : '❌') . '</div>
                <div>REST API: ' . (!empty($settings['rest_api_enabled']) ? '✅' : '❌') . '</div>
                <div>Frontend: ❌ Blocked</div>
                <div>Admin: ✅ Active</div>
            </div>
            <p>
                <a href="' . admin_url('options-general.php?page=headless-mode') . '" class="button button-primary">Configure</a>
                <a href="' . home_url('/') . '" target="_blank" class="button">View Blocked Page</a>
            </p>
        </div>';
    }
}