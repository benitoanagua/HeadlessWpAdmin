<?php

/**
 * CSS Variables Partial
 * 
 * @var array<string, mixed> $settings
 */
?>
<style id="headless-dynamic-vars">
    :root {
        --headless-primary: <?php echo esc_attr($settings['background_color'] ?? '#667eea'); ?>;
        --headless-gradient: <?php echo esc_attr($settings['background_gradient'] ?? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'); ?>;
        --headless-border-radius: 1.5rem;
        --headless-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    <?php if (!empty($settings['custom_css'])): ?>
    /* Custom CSS */
    <?php echo wp_strip_all_tags($settings['custom_css']); ?><?php endif; ?>
</style>