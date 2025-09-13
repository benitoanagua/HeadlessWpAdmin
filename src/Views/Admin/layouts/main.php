<?php

/**
 * Admin Main Layout Template
 * 
 * @var array<string, mixed> $settings
 * @var string $active_tab
 * @var array<string, string> $tabs
 * @var string $content
 */
?>
<div class="wrap headless-admin-wrap bg-gray-50 min-h-screen">
    <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center">
            <span class="mr-2">🚀</span>
            <?php echo __('Headless WordPress - Configuration', 'headless-wp-admin'); ?>
        </h1>
    </header>

    <nav class="bg-white border-b border-gray-200 px-6">
        <div class="flex space-x-8">
            <?php foreach ($tabs as $tab => $label): ?>
                <a href="<?php echo esc_url(add_query_arg('tab', $tab)); ?>"
                    class="<?php echo $active_tab === $tab
                                ? 'border-blue-500 text-blue-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>
                   py-4 px-1 border-b-2 font-medium text-sm">
                    <?php echo esc_html($label); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>

    <main class="px-6 py-6">
        <form method="post" action="" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <?php wp_nonce_field('headless_settings_save', 'headless_nonce'); ?>

            <div class="space-y-8">
                <?php echo $content; ?>
            </div>

            <footer class="mt-8 pt-6 border-t border-gray-200 flex items-center gap-3">
                <button type="submit" name="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <?php echo __('Save Configuration', 'headless-wp-admin'); ?>
                </button>

                <button type="button" id="preview-blocked-page" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md font-medium hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    👁️ <?php echo __('Preview', 'headless-wp-admin'); ?>
                </button>

                <button type="button" id="reset-settings" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md font-medium hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    🔄 <?php echo __('Reset', 'headless-wp-admin'); ?>
                </button>
            </footer>
        </form>
    </main>
</div>

<?php
// Include admin scripts
wp_enqueue_style('wp-color-picker');
wp_enqueue_script('wp-color-picker');
?>

<script>
    jQuery(document).ready(function($) {
        // Initialize color picker
        $('.color-picker').wpColorPicker();

        // Test GraphQL endpoint
        $('#test-graphql').click(function() {
            $.post(ajaxurl, {
                action: 'headless_test_endpoint',
                endpoint: 'graphql'
            }, function(response) {
                if (response.success) {
                    alert('GraphQL endpoint is working correctly');
                } else {
                    alert('Error: ' + response.data);
                }
            });
        });

        // Reset settings
        $('#reset-settings').click(function() {
            if (confirm('Are you sure you want to reset all settings?')) {
                $.post(ajaxurl, {
                    action: 'headless_reset_settings',
                    nonce: '<?php echo wp_create_nonce("headless_reset"); ?>'
                }, function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.data);
                    }
                });
            }
        });

        // Preview blocked page
        $('#preview-blocked-page').click(function() {
            window.open('<?php echo home_url(); ?>', '_blank');
        });
    });
</script>