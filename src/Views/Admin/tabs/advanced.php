<?php

/**
 * Advanced Settings Tab
 * 
 * @var array<string, mixed> $settings
 */
?>
<div class="space-y-8">
    <!-- Advanced Configuration Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <span class="mr-2">⚙️</span>
                <?php echo __('Advanced Configuration', 'headless-wp-admin'); ?>
            </h3>
        </div>
        <div class="px-6 py-6 space-y-6">
            <div>
                <label for="custom_redirect_rules" class="block text-sm font-medium text-gray-900 mb-2">
                    <?php echo __('Custom Redirect Rules', 'headless-wp-admin'); ?>
                </label>
                <textarea name="custom_redirect_rules" id="custom_redirect_rules" rows="6"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="# Example:&#10;/old-page -> /new-page&#10;/blog/* -> https://external-blog.com/*"><?php echo esc_textarea($settings['custom_redirect_rules'] ?? ''); ?></textarea>
                <p class="mt-1 text-sm text-gray-500">
                    <?php echo __('One rule per line. Use -> to separate source and destination.', 'headless-wp-admin'); ?>
                </p>
            </div>

            <div>
                <label for="custom_headers" class="block text-sm font-medium text-gray-900 mb-2">
                    <?php echo __('Custom HTTP Headers', 'headless-wp-admin'); ?>
                </label>
                <textarea name="custom_headers" id="custom_headers" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="X-Custom-Header: value&#10;Cache-Control: no-cache"><?php echo esc_textarea($settings['custom_headers'] ?? ''); ?></textarea>
                <p class="mt-1 text-sm text-gray-500">
                    <?php echo __('One header per line in "Name: Value" format.', 'headless-wp-admin'); ?>
                </p>
            </div>

            <div>
                <label for="webhook_urls" class="block text-sm font-medium text-gray-900 mb-2">
                    <?php echo __('Webhook URLs', 'headless-wp-admin'); ?>
                </label>
                <textarea name="webhook_urls" id="webhook_urls" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="https://api.example.com/webhook1&#10;https://api.example.com/webhook2"><?php echo esc_textarea($settings['webhook_urls'] ?? ''); ?></textarea>
                <p class="mt-1 text-sm text-gray-500">
                    <?php echo __('Will be called when content is updated (experimental)', 'headless-wp-admin'); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Maintenance Tools Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <span class="mr-2">🔧</span>
                <?php echo __('Maintenance Tools', 'headless-wp-admin'); ?>
            </h3>
        </div>
        <div class="px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <button type="button" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md font-medium hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    🗑️ <?php echo __('Clear GraphQL Cache', 'headless-wp-admin'); ?>
                </button>

                <button type="button" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md font-medium hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                    onclick="window.open('<?php echo home_url('/wp-json/'); ?>', '_blank')">
                    🔍 <?php echo __('Explore REST API', 'headless-wp-admin'); ?>
                </button>

                <button type="button" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md font-medium hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                    onclick="window.open('<?php echo home_url('/graphql'); ?>', '_blank')">
                    ⚡ <?php echo __('Open GraphQL', 'headless-wp-admin'); ?>
                </button>

                <button type="button" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md font-medium hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    📤 <?php echo __('Export Config', 'headless-wp-admin'); ?>
                </button>
            </div>
        </div>
    </div>

    <!-- System Information Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <span class="mr-2">📋</span>
                <?php echo __('System Information', 'headless-wp-admin'); ?>
            </h3>
        </div>
        <div class="px-6 py-6">
            <table class="w-full divide-y divide-gray-200">
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo __('WordPress Version:', 'headless-wp-admin'); ?></td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"><?php echo get_bloginfo('version'); ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo __('PHP Version:', 'headless-wp-admin'); ?></td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"><?php echo PHP_VERSION; ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo __('GraphQL Plugin:', 'headless-wp-admin'); ?></td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"><?php echo class_exists('WPGraphQL') ? '✅ ' . __('Installed', 'headless-wp-admin') : '❌ ' . __('Not installed', 'headless-wp-admin'); ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo __('Headless Plugin Version:', 'headless-wp-admin'); ?></td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">2.0</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo __('Debug Mode:', 'headless-wp-admin'); ?></td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"><?php echo WP_DEBUG ? '✅ ' . __('Active', 'headless-wp-admin') : '❌ ' . __('Disabled', 'headless-wp-admin'); ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo __('Frontend URL:', 'headless-wp-admin'); ?></td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"><code><?php echo home_url(); ?></code></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-9"><?php echo __('GraphQL URL:', 'headless-wp-admin'); ?></td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"><code><?php echo home_url('/graphql'); ?></code></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>