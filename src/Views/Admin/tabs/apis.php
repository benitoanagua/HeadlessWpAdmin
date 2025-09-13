<?php

/**
 * APIs Settings Tab
 * 
 * @var array<string, mixed> $settings
 */
?>
<div class="space-y-8">
    <!-- GraphQL API Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <span class="mr-2">⚡</span>
                <?php echo __('GraphQL API', 'headless-wp-admin'); ?>
            </h3>
        </div>
        <div class="px-6 py-6 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-1">
                        <?php echo __('Enable GraphQL API', 'headless-wp-admin'); ?>
                    </label>
                    <p class="text-sm text-gray-500">
                        <?php echo __('Endpoint:', 'headless-wp-admin'); ?>
                        <code><?php echo home_url('/graphql'); ?></code>
                    </p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="graphql_enabled" value="1"
                        class="sr-only peer" <?php checked($settings['graphql_enabled']); ?>>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>

            <div class="flex items-center">
                <input id="graphql_cors_enabled" name="graphql_cors_enabled" type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    <?php checked($settings['graphql_cors_enabled']); ?>>
                <label for="graphql_cors_enabled" class="ml-3 block text-sm font-medium text-gray-900">
                    <?php echo __('Enable CORS for GraphQL', 'headless-wp-admin'); ?>
                </label>
            </div>

            <div>
                <label for="graphql_cors_origins" class="block text-sm font-medium text-gray-900 mb-2">
                    <?php echo __('Allowed CORS Origins (GraphQL)', 'headless-wp-admin'); ?>
                </label>
                <textarea name="graphql_cors_origins" id="graphql_cors_origins" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?php echo esc_textarea($settings['graphql_cors_origins']); ?></textarea>
                <p class="mt-1 text-sm text-gray-500">
                    <?php echo __('One origin per line (e.g., https://localhost:3000)', 'headless-wp-admin'); ?>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-center">
                    <input id="graphql_introspection" name="graphql_introspection" type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        <?php checked($settings['graphql_introspection']); ?>>
                    <label for="graphql_introspection" class="ml-3 block text-sm font-medium text-gray-900">
                        <?php echo __('Enable Introspection', 'headless-wp-admin'); ?>
                    </label>
                </div>

                <div class="flex items-center">
                    <input id="graphql_tracing" name="graphql_tracing" type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        <?php checked($settings['graphql_tracing']); ?>>
                    <label for="graphql_tracing" class="ml-3 block text-sm font-medium text-gray-900">
                        <?php echo __('Enable Tracing', 'headless-wp-admin'); ?>
                    </label>
                </div>

                <div class="flex items-center">
                    <input id="graphql_caching" name="graphql_caching" type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        <?php checked($settings['graphql_caching']); ?>>
                    <label for="graphql_caching" class="ml-3 block text-sm font-medium text-gray-900">
                        <?php echo __('Enable Query Caching', 'headless-wp-admin'); ?>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- REST API Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <span class="mr-2">🔌</span>
                <?php echo __('REST API', 'headless-wp-admin'); ?>
            </h3>
        </div>
        <div class="px-6 py-6 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-1">
                        <?php echo __('Enable REST API', 'headless-wp-admin'); ?>
                    </label>
                    <p class="text-sm text-gray-500">
                        <?php echo __('Endpoint:', 'headless-wp-admin'); ?>
                        <code><?php echo home_url('/wp-json/'); ?></code>
                    </p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="rest_api_enabled" value="1"
                        class="sr-only peer" <?php checked($settings['rest_api_enabled']); ?>>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>

            <div class="flex items-center">
                <input id="rest_api_auth_required" name="rest_api_auth_required" type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    <?php checked($settings['rest_api_auth_required']); ?>>
                <label for="rest_api_auth_required" class="ml-3 block text-sm font-medium text-gray-900">
                    <?php echo __('Require Authentication for REST API', 'headless-wp-admin'); ?>
                </label>
            </div>

            <div>
                <label for="rest_api_allowed_routes" class="block text-sm font-medium text-gray-900 mb-2">
                    <?php echo __('Allowed REST Routes', 'headless-wp-admin'); ?>
                </label>
                <textarea name="rest_api_allowed_routes" id="rest_api_allowed_routes" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?php echo esc_textarea($settings['rest_api_allowed_routes']); ?></textarea>
                <p class="mt-1 text-sm text-gray-500">
                    <?php echo __('One route per line. Leave empty to allow all routes.', 'headless-wp-admin'); ?>
                </p>
            </div>

            <div>
                <label for="rest_api_cors_origins" class="block text-sm font-medium text-gray-900 mb-2">
                    <?php echo __('Allowed CORS Origins (REST API)', 'headless-wp-admin'); ?>
                </label>
                <textarea name="rest_api_cors_origins" id="rest_api_cors_origins" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?php echo esc_textarea($settings['rest_api_cors_origins']); ?></textarea>
                <p class="mt-1 text-sm text-gray-500">
                    <?php echo __('One origin per line for CORS configuration', 'headless-wp-admin'); ?>
                </p>
            </div>
        </div>
    </div>
</div>