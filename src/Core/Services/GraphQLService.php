<?php

namespace HeadlessWPAdmin\Core\Services;

use HeadlessWPAdmin\Core\HeadlessHandler;

class GraphQLService
{
    private HeadlessHandler $headlessHandler;

    public function __construct(HeadlessHandler $headlessHandler)
    {
        $this->headlessHandler = $headlessHandler;
    }

    public function configure(): void
    {
        if (!$this->headlessHandler->get_setting('graphql_enabled')) {
            // Deshabilitar GraphQL si está desactivado
            add_filter('graphql_enabled', '__return_false');
            return;
        }

        // CORS para GraphQL
        if ($this->headlessHandler->get_setting('graphql_cors_enabled')) {
            $this->setup_graphql_cors();
        }

        // Introspección
        add_filter('graphql_introspection_enabled', function () {
            return $this->headlessHandler->get_setting('graphql_introspection');
        });

        // Tracing
        add_filter('graphql_tracing_enabled', function () {
            return $this->headlessHandler->get_setting('graphql_tracing');
        });

        // Caching
        if ($this->headlessHandler->get_setting('graphql_caching')) {
            add_filter('graphql_query_cache_enabled', '__return_true');
        }
    }

    private function setup_graphql_cors(): void
    {
        add_filter('graphql_cors_allowed_origins', function ($origins) {
            $custom_origins = array_filter(explode("\n", $this->headlessHandler->get_setting('graphql_cors_origins')));
            return array_merge($origins, $custom_origins);
        });

        add_filter('graphql_cors_allow_credentials', '__return_true');

        add_filter('graphql_cors_allowed_headers', function ($headers) {
            return array_merge($headers, [
                'Authorization',
                'Content-Type',
                'X-Requested-With',
                'X-WP-Nonce',
                'Cache-Control',
                'Accept-Language',
                'Apollo-Require-Preflight'
            ]);
        });
    }
}
