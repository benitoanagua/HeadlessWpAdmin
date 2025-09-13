<?php

/**
 * Security Settings Tab
 * 
 * @var array<string, mixed> $settings
 */
?>
<div class="space-y-8">
    <!-- Security Configuration Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <span class="mr-2">🔒</span>
                <?php echo __('Security Configuration', 'headless-wp-admin'); ?>
            </h3>
        </div>
        <div class="px-6 py-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-center">
                    <input id="security_headers_enabled" name="security_headers_enabled" type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        <?php checked($settings['security_headers_enabled']); ?>>
                    <label for="security_headers_enabled" class="ml-3 block text-sm font-medium text-gray-900">
                        <?php echo __('Enable Security Headers', 'headless-wp-admin'); ?>
                    </label>
                </div>

                <div class="flex items-center">
                    <input id="block_theme_access" name="block_theme_access" type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        <?php checked($settings['block_theme_access']); ?>>
                    <label for="block_theme_access" class="ml-3 block text-sm font-medium text-gray-900">
                        <?php echo __('Block Direct Theme File Access', 'headless-wp-admin'); ?>
                    </label>
                </div>

                <div class="flex items-center">
                    <input id="rate_limiting_enabled" name="rate_limiting_enabled" type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        <?php checked($settings['rate_limiting_enabled']); ?>>
                    <label for="rate_limiting_enabled" class="ml-3 block text-sm font-medium text-gray-900">
                        <?php echo __('Enable Basic Rate Limiting', 'headless-wp-admin'); ?>
                    </label>
                </div>

                <div class="flex items-center">
                    <input id="debug_logging" name="debug_logging" type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        <?php checked($settings['debug_logging']); ?>>
                    <label for="debug_logging" class="ml-3 block text-sm font-medium text-gray-900">
                        <?php echo __('Enable Debug Logging', 'headless-wp-admin'); ?>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Block Statistics Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <span class="mr-2">📊</span>
                <?php echo __('Block Statistics (Last 24h)', 'headless-wp-admin'); ?>
            </h3>
        </div>
        <div class="px-6 py-6">
            <div id="security-stats" class="text-center py-8">
                <p class="text-gray-500 italic">
                    <?php echo __('Function in development - Blocked access statistics coming soon', 'headless-wp-admin'); ?>
                </p>
            </div>
        </div>
    </div>
</div>