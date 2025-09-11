<?php
/**
 * Blocked Page Template
 * 
 * This template can be overridden by copying it to your-theme/headless-wp-admin/blocked-page.php
 * 
 * @package HeadlessWPAdmin
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get settings
$settings = get_option('headless_wp_settings', array());
$admin_url = admin_url();
$graphql_url = home_url('/graphql');
$site_name = get_bloginfo('name');

// Build styles
$bg_gradient = !empty($settings['blocked_page_background_gradient']) 
    ? $settings['blocked_page_background_gradient'] 
    : 'linear-gradient(135deg, ' . ($settings['blocked_page_background_color'] ?? '#667eea') . ' 0%, #764ba2 100%)';

?>
<!DOCTYPE html>
<html lang="<?php echo get_locale(); ?>" class="headless-blocked-page">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html($settings['blocked_page_title'] ?? 'Headless Mode Active'); ?> - <?php echo esc_html($site_name); ?></title>
    <meta name="robots" content="noindex,nofollow">
    <style>
        <?php include HEADLESS_WP_ADMIN_PLUGIN_PATH . 'public/css/blocked-page.css'; ?>
        
        /* Dynamic styles */
        body {
            background: <?php echo $bg_gradient; ?>;
        }
        
        .endpoint::before {
            background: <?php echo $bg_gradient; ?>;
        }
        
        .btn {
            background: <?php echo $bg_gradient; ?>;
        }
        
        <?php echo $settings['blocked_page_custom_css'] ?? ''; ?>
    </style>
</head>
<body>
    <div class="container">
        <?php if (!empty($settings['blocked_page_logo_url'])) : ?>
            <div class="logo">
                <img src="<?php echo esc_url($settings['blocked_page_logo_url']); ?>" alt="<?php echo esc_attr($site_name); ?> Logo">
            </div>
        <?php endif; ?>
        
        <?php if (!empty($settings['blocked_page_icon'])) : ?>
            <div class="icon"><?php echo esc_html($settings['blocked_page_icon']); ?></div>
        <?php endif; ?>
        
        <h1><?php echo esc_html($settings['blocked_page_title'] ?? 'Headless Mode Active'); ?></h1>
        
        <?php if (!empty($settings['blocked_page_subtitle'])) : ?>
            <p class="subtitle"><?php echo esc_html($settings['blocked_page_subtitle']); ?></p>
        <?php endif; ?>
        
        <?php if (!empty($settings['blocked_page_message'])) : ?>
            <div class="message"><?php echo wp_kses_post(wpautop($settings['blocked_page_message'])); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($settings['blocked_page_show_status_info'])) : ?>
            <div class="status-grid">
                <?php if (!empty($settings['graphql_enabled'])) : ?>
                    <div class="status-item">
                        <div class="status active">✅ GraphQL API</div>
                        <small>Available for queries</small>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($settings['rest_api_enabled'])) : ?>
                    <div class="status-item">
                        <div class="status active">✅ REST API</div>
                        <small>Available with configuration</small>
                    </div>
                <?php endif; ?>
                
                <div class="status-item">
                    <div class="status active">✅ Administration</div>
                    <small>Control panel active</small>
                </div>
                
                <div class="status-item">
                    <div class="status blocked">❌ Frontend</div>
                    <small>Public access disabled</small>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($settings['blocked_page_show_graphql_link']) && !empty($settings['graphql_enabled'])) : ?>
            <div class="endpoint">
                <strong>🔌 GraphQL Endpoint</strong>
                <code><?php echo esc_url($graphql_url); ?></code>
            </div>
        <?php endif; ?>
        
        <div class="buttons">
            <?php if (!empty($settings['blocked_page_show_admin_link'])) : ?>
                <a href="<?php echo esc_url($admin_url); ?>" class="btn">
                    <span>⚙️</span> Administration
                </a>
            <?php endif; ?>
            
            <?php if (!empty($settings['blocked_page_show_graphql_link']) && !empty($settings['graphql_enabled'])) : ?>
                <a href="<?php echo esc_url($graphql_url); ?>" class="btn" target="_blank">
                    <span>⚡</span> GraphQL
                </a>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($settings['blocked_page_contact_info'])) : ?>
            <div class="contact-info">
                <?php echo wp_kses_post(wpautop($settings['blocked_page_contact_info'])); ?>
            </div>
        <?php endif; ?>
        
        <div class="footer">
            <p><strong><?php echo esc_html($site_name); ?></strong> - Headless CMS</p>
            <small>Develop your frontend using available APIs</small>
        </div>
    </div>
</body>
</html>