<?php

/**
 * General Settings Tab
 * 
 * @var array<string, mixed> $settings
 */
?>
<div class="space-y-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900"><?php echo __('General Configuration', 'headless-wp-admin'); ?></h3>
        </div>
        <div class="px-6 py-6 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-1">
                        <?php echo __('Show Custom Blocked Page', 'headless-wp-admin'); ?>
                    </label>
                    <p class="text-sm text-gray-500">
                        <?php echo __('If disabled, a standard 403 error will be shown.', 'headless-wp-admin'); ?>
                    </p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="blocked_page_enabled" value="1"
                        class="sr-only peer" <?php checked($settings['blocked_page_enabled']); ?>>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>

            <div>
                <label for="allowed_paths" class="block text-sm font-medium text-gray-900 mb-2">
                    <?php echo __('Allowed Paths', 'headless-wp-admin'); ?>
                </label>
                <textarea name="allowed_paths" id="allowed_paths" rows="6"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?php echo esc_textarea($settings['allowed_paths']); ?></textarea>
                <p class="mt-1 text-sm text-gray-500">
                    <?php echo __('One path per line. These paths will not be blocked by the headless system.', 'headless-wp-admin'); ?>
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900"><?php echo __('WordPress Cleanup', 'headless-wp-admin'); ?></h3>
        </div>
        <div class="px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-center">
                    <input id="disable_feeds" name="disable_feeds" type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        <?php checked($settings['disable_feeds']); ?>>
                    <label for="disable_feeds" class="ml-3 block text-sm font-medium text-gray-900">
                        <?php echo __('Disable RSS Feeds', 'headless-wp-admin'); ?>
                    </label>
                </div>

                <div class="flex items-center">
                    <input id="disable_sitemaps" name="disable_sitemaps" type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        <?php checked($settings['disable_sitemaps']); ?>>
                    <label for="disable_sitemaps" class="ml-3 block text-sm font-medium text-gray-900">
                        <?php echo __('Disable XML Sitemaps', 'headless-wp-admin'); ?>
                    </label>
                </div>

                <div class="flex items-center">
                    <input id="disable_comments" name="disable_comments" type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        <?php checked($settings['disable_comments']); ?>>
                    <label for="disable_comments" class="ml-3 block text-sm font-medium text-gray-900">
                        <?php echo __('Disable Comments', 'headless-wp-admin'); ?>
                    </label>
                </div>

                <div class="flex items-center">
                    <input id="disable_embeds" name="disable_embeds" type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        <?php checked($settings['disable_embeds']); ?>>
                    <label for="disable_embeds" class="ml-3 block text-sm font-medium text-gray-900">
                        <?php echo __('Disable oEmbed', 'headless-wp-admin'); ?>
                    </label>
                </div>

                <div class="flex items-center">
                    <input id="disable_emojis" name="disable_emojis" type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        <?php checked($settings['disable_emojis']); ?>>
                    <label for="disable_emojis" class="ml-3 block text-sm font-medium text-gray-900">
                        <?php echo __('Disable Emojis', 'headless-wp-admin'); ?>
                    </label>
                </div>

                <div class="flex items-center">
                    <input id="clean_wp_head" name="clean_wp_head" type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        <?php checked($settings['clean_wp_head']); ?>>
                    <label for="clean_wp_head" class="ml-3 block text-sm font-medium text-gray-900">
                        <?php echo __('Clean wp_head()', 'headless-wp-admin'); ?>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>