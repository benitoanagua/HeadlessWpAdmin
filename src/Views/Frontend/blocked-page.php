<?php

/**
 * Blocked Page Template
 * 
 * @var array<string, mixed> $settings
 * @var string $admin_url
 * @var string $graphql_url
 * @var bool $is_graphql_enabled
 * @var bool $is_rest_enabled
 */
?>
<!DOCTYPE html>
<html lang="<?php echo esc_attr(get_locale()); ?>" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html($settings['title']); ?></title>
    <meta name="robots" content="noindex,nofollow">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '<?php echo esc_js($settings['background_color']); ?>',
                    }
                }
            }
        }
    </script>

    <style>
        .gradient-bg {
            background: <?php echo $settings['background_gradient']; ?>;
        }

        <?php if (!empty($settings['custom_css'])): ?>
        /* Custom CSS */
        <?php echo wp_strip_all_tags($settings['custom_css']); ?><?php endif; ?>
    </style>
</head>

<body class="h-full">
    <div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 gradient-bg">
        <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-xl shadow-lg">
            <div class="text-center">
                <?php if (!empty($settings['logo_url'])): ?>
                    <img class="mx-auto h-24 w-auto"
                        src="<?php echo esc_url($settings['logo_url']); ?>"
                        alt="Logo">
                <?php else: ?>
                    <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-primary/10">
                        <span class="text-4xl"><?php echo esc_html($settings['icon']); ?></span>
                    </div>
                <?php endif; ?>

                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    <?php echo esc_html($settings['title']); ?>
                </h2>

                <?php if (!empty($settings['subtitle'])): ?>
                    <p class="mt-2 text-sm text-gray-600">
                        <?php echo esc_html($settings['subtitle']); ?>
                    </p>
                <?php endif; ?>
            </div>

            <?php if (!empty($settings['message'])): ?>
                <div class="mt-8 bg-blue-50 rounded-lg p-4">
                    <div class="prose prose-blue max-w-none text-sm">
                        <?php echo wp_kses_post(wpautop($settings['message'])); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($settings['show_status_info']): ?>
                <div class="mt-8 grid grid-cols-1 gap-4">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">
                                    Headless Mode: ACTIVE
                                </p>
                            </div>
                        </div>
                    </div>

                    <?php if ($is_graphql_enabled): ?>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">
                                        GraphQL API: ENABLED
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($is_rest_enabled): ?>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">
                                        REST API: ENABLED
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="mt-8 flex flex-col gap-3">
                <?php if ($settings['show_admin_link']): ?>
                    <a href="<?php echo esc_url($admin_url); ?>"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        📊 <?php echo __('Go to Admin Panel', 'headless-wp-admin'); ?>
                    </a>
                <?php endif; ?>

                <?php if ($settings['show_graphql_link'] && $is_graphql_enabled): ?>
                    <a href="<?php echo esc_url($graphql_url); ?>"
                        target="_blank"
                        class="group relative w-full flex justify-center py-3 px-4 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        ⚡ <?php echo __('GraphQL API', 'headless-wp-admin'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>