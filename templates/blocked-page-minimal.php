<?php
/**
 * Minimal Blocked Page Template
 * 
 * @package HeadlessWPAdmin
 */

if (!defined('ABSPATH')) {
    exit;
}

$settings = get_option('headless_wp_settings', array());
$site_name = get_bloginfo('name');

?>
<!DOCTYPE html>
<html lang="<?php echo get_locale(); ?>" class="headless-blocked-page headless-minimal">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html($settings['blocked_page_title'] ?? 'Headless Mode'); ?> - <?php echo esc_html($site_name); ?></title>
    <meta name="robots" content="noindex,nofollow">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2c3e50;
            line-height: 1.6;
            padding: 20px;
        }
        .container {
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #2c3e50;
        }
        p {
            margin-bottom: 2rem;
            color: #6c757d;
        }
        .admin-link {
            display: inline-block;
            padding: 10px 20px;
            background: #007cba;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
        }
        .admin-link:hover {
            background: #006ba1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo esc_html($settings['blocked_page_title'] ?? 'Headless Mode Active'); ?></h1>
        <p><?php echo esc_html($settings['blocked_page_message'] ?? 'This site is running in headless mode. Frontend access is disabled.'); ?></p>
        <a href="<?php echo admin_url(); ?>" class="admin-link">Access Admin Panel</a>
    </div>
</body>
</html>