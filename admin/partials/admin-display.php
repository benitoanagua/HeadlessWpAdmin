<div class="wrap">
    <h1>🚀 Headless WordPress - Configuration</h1>
    
    <h2 class="nav-tab-wrapper">
        <a href="?page=headless-mode&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General</a>
        <a href="?page=headless-mode&tab=apis" class="nav-tab <?php echo $active_tab == 'apis' ? 'nav-tab-active' : ''; ?>">APIs</a>
        <a href="?page=headless-mode&tab=blocked-page" class="nav-tab <?php echo $active_tab == 'blocked-page' ? 'nav-tab-active' : ''; ?>">Blocked Page</a>
        <a href="?page=headless-mode&tab=security" class="nav-tab <?php echo $active_tab == 'security' ? 'nav-tab-active' : ''; ?>">Security</a>
        <a href="?page=headless-mode&tab=advanced" class="nav-tab <?php echo $active_tab == 'advanced' ? 'nav-tab-active' : ''; ?>">Advanced</a>
    </h2>

    <form method="post" action="">
        <?php wp_nonce_field('headless_settings_save', 'headless_nonce'); ?>
        
        <?php
        switch($active_tab) {
            case 'general':
                include HEADLESS_WP_ADMIN_PLUGIN_PATH . 'admin/partials/tab-general.php';
                break;
            case 'apis':
                include HEADLESS_WP_ADMIN_PLUGIN_PATH . 'admin/partials/tab-apis.php';
                break;
            case 'blocked-page':
                include HEADLESS_WP_ADMIN_PLUGIN_PATH . 'admin/partials/tab-blocked-page.php';
                break;
            case 'security':
                include HEADLESS_WP_ADMIN_PLUGIN_PATH . 'admin/partials/tab-security.php';
                break;
            case 'advanced':
                include HEADLESS_WP_ADMIN_PLUGIN_PATH . 'admin/partials/tab-advanced.php';
                break;
        }
        ?>

        <div class="headless-admin-footer">
            <?php submit_button('Save Configuration', 'primary', 'submit', false); ?>
            <button type="button" id="preview-blocked-page" class="button">👁️ Preview</button>
            <button type="button" id="reset-settings" class="button button-secondary" style="margin-left: 10px;">🔄 Reset</button>
        </div>
    </form>
</div>