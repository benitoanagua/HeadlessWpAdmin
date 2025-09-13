<?php

/**
 * Blocked Page Settings Tab
 * 
 * @var array<string, mixed> $settings
 */
?>
<div class="space-y-8">
    <!-- Page Appearance Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <span class="mr-2">🎨</span>
                <?php echo __('Page Appearance', 'headless-wp-admin'); ?>
            </h3>
        </div>
        <div class="px-6 py-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="blocked_page_title" class="block text-sm font-medium text-gray-900 mb-2">
                        <?php echo __('Main Title', 'headless-wp-admin'); ?>
                    </label>
                    <input type="text" name="blocked_page_title" id="blocked_page_title"
                        value="<?php echo esc_attr($settings['blocked_page_title']); ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="blocked_page_subtitle" class="block text-sm font-medium text-gray-900 mb-2">
                        <?php echo __('Subtitle', 'headless-wp-admin'); ?>
                    </label>
                    <input type="text" name="blocked_page_subtitle" id="blocked_page_subtitle"
                        value="<?php echo esc_attr($settings['blocked_page_subtitle']); ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div>
                <label for="blocked_page_message" class="block text-sm font-medium text-gray-900 mb-2">
                    <?php echo __('Main Message', 'headless-wp-admin'); ?>
                </label>
                <textarea name="blocked_page_message" id="blocked_page_message" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?php echo esc_textarea($settings['blocked_page_message']); ?></textarea>
                <p class="mt-1 text-sm text-gray-500">
                    <?php echo __('You can use basic HTML. Leave empty to hide message.', 'headless-wp-admin'); ?>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="blocked_page_icon" class="block text-sm font-medium text-gray-900 mb-2">
                        <?php echo __('Icon/Emoji', 'headless-wp-admin'); ?>
                    </label>
                    <input type="text" name="blocked_page_icon" id="blocked_page_icon"
                        value="<?php echo esc_attr($settings['blocked_page_icon']); ?>" maxlength="10"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">
                        <?php echo __('Emoji or symbol to display prominently', 'headless-wp-admin'); ?>
                    </p>
                </div>

                <div>
                    <label for="blocked_page_logo_url" class="block text-sm font-medium text-gray-900 mb-2">
                        <?php echo __('Logo URL', 'headless-wp-admin'); ?>
                    </label>
                    <input type="url" name="blocked_page_logo_url" id="blocked_page_logo_url"
                        value="<?php echo esc_url($settings['blocked_page_logo_url']); ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">
                        <?php echo __('Full URL of the logo image', 'headless-wp-admin'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Colors and Design Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <span class="mr-2">🎨</span>
                <?php echo __('Colors and Design', 'headless-wp-admin'); ?>
            </h3>
        </div>
        <div class="px-6 py-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="blocked_page_background_color" class="block text-sm font-medium text-gray-900 mb-2">
                        <?php echo __('Base Background Color', 'headless-wp-admin'); ?>
                    </label>
                    <input type="text" name="blocked_page_background_color" id="blocked_page_background_color"
                        value="<?php echo esc_attr($settings['blocked_page_background_color']); ?>"
                        class="color-picker w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="blocked_page_background_gradient" class="block text-sm font-medium text-gray-900 mb-2">
                        <?php echo __('Background Gradient (CSS)', 'headless-wp-admin'); ?>
                    </label>
                    <input type="text" name="blocked_page_background_gradient" id="blocked_page_background_gradient"
                        value="<?php echo esc_attr($settings['blocked_page_background_gradient']); ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">
                        <?php echo __('Example: linear-gradient(135deg, #667eea 0%, #764ba2 100%)', 'headless-wp-admin'); ?>
                    </p>
                </div>
            </div>

            <div>
                <label for="blocked_page_custom_css" class="block text-sm font-medium text-gray-900 mb-2">
                    <?php echo __('Custom CSS', 'headless-wp-admin'); ?>
                </label>
                <textarea name="blocked_page_custom_css" id="blocked_page_custom_css" rows="8"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?php echo esc_textarea($settings['blocked_page_custom_css']); ?></textarea>
                <p class="mt-1 text-sm text-gray-500">
                    <?php echo __('Additional CSS to fully customize the page', 'headless-wp-admin'); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Content and Links Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <span class="mr-2">📝</span>
                <?php echo __('Content and Links', 'headless-wp-admin'); ?>
            </h3>
        </div>
        <div class="px-6 py-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="flex items-center">
                    <input id="blocked_page_show_admin_link" name="blocked_page_show_admin_link" type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        <?php checked($settings['blocked_page_show_admin_link']); ?>>
                    <label for="blocked_page_show_admin_link" class="ml-3 block text-sm font-medium text-gray-900">
                        <?php echo __('Show Admin Link', 'headless-wp-admin'); ?>
                    </label>
                </div>

                <div class="flex items-center">
                    <input id="blocked_page_show_graphql_link" name="blocked_page_show_graphql_link" type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        <?php checked($settings['blocked_page_show_graphql_link']); ?>>
                    <label for="blocked_page_show_graphql_link" class="ml-3 block text-sm font-medium text-gray-900">
                        <?php echo __('Show GraphQL Link', 'headless-wp-admin'); ?>
                    </label>
                </div>

                <div class="flex items-center">
                    <input id="blocked_page_show_status_info" name="blocked_page_show_status_info" type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        <?php checked($settings['blocked_page_show_status_info']); ?>>
                    <label for="blocked_page_show_status_info" class="ml-3 block text-sm font-medium text-gray-900">
                        <?php echo __('Show API Status Info', 'headless-wp-admin'); ?>
                    </label>
                </div>
            </div>

            <div>
                <label for="blocked_page_contact_info" class="block text-sm font-medium text-gray-9 mb-2">
                    <?php echo __('Contact Information', 'headless-wp-admin'); ?>
                </label>
                <textarea name="blocked_page_contact_info" id="blocked_page_contact_info" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?php echo esc_textarea($settings['blocked_page_contact_info']); ?></textarea>
                <p class="mt-1 text-sm text-gray-500">
                    <?php echo __('Additional information that will appear at the bottom of the page (HTML allowed)', 'headless-wp-admin'); ?>
                </p>
            </div>
        </div>
    </div>
</div>