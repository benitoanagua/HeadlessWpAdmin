<?php
namespace HeadlessWPAdmin;

class HeadlessWPAdmin {
    private $loader;
    private $plugin_name;
    private $version;

    public function __construct() {
        $this->plugin_name = 'headless-wp-admin';
        $this->version = HEADLESS_WP_ADMIN_VERSION;
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_core_hooks();
    }

    private function load_dependencies() {
        require_once HEADLESS_WP_ADMIN_PLUGIN_PATH . 'includes/class-loader.php';
        require_once HEADLESS_WP_ADMIN_PLUGIN_PATH . 'includes/class-i18n.php';
        require_once HEADLESS_WP_ADMIN_PLUGIN_PATH . 'admin/class-admin.php';
        require_once HEADLESS_WP_ADMIN_PLUGIN_PATH . 'public/class-public.php';
        require_once HEADLESS_WP_ADMIN_PLUGIN_PATH . 'core/class-rest-api.php';
        require_once HEADLESS_WP_ADMIN_PLUGIN_PATH . 'core/class-graphql.php';
        require_once HEADLESS_WP_ADMIN_PLUGIN_PATH . 'core/class-security.php';
        
        $this->loader = new Loader();
    }

    private function set_locale() {
        $plugin_i18n = new i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    private function define_admin_hooks() {
        $plugin_admin = new Admin\Admin($this->get_plugin_name(), $this->get_version());
        
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_admin_menu');
        $this->loader->add_action('admin_init', $plugin_admin, 'settings_init');
        $this->loader->add_action('admin_notices', $plugin_admin, 'admin_notices');
        $this->loader->add_action('wp_ajax_headless_test_endpoint', $plugin_admin, 'test_endpoint');
        $this->loader->add_action('wp_ajax_headless_reset_settings', $plugin_admin, 'reset_settings');
    }

    private function define_public_hooks() {
        $plugin_public = new Public\Public($this->get_plugin_name(), $this->get_version());
        
        $this->loader->add_action('init', $plugin_public, 'init_headless_mode', 0);
        $this->loader->add_action('template_redirect', $plugin_public, 'handle_frontend_requests', 0);
        $this->loader->add_action('send_headers', $plugin_public, 'add_security_headers');
    }

    private function define_core_hooks() {
        $rest_api = new Core\RestAPI();
        $graphql = new Core\GraphQL();
        $security = new Core\Security();
        
        // REST API hooks
        $this->loader->add_action('rest_api_init', $rest_api, 'setup_cors');
        $this->loader->add_filter('rest_authentication_errors', $rest_api, 'require_authentication');
        $this->loader->add_filter('rest_pre_dispatch', $rest_api, 'filter_routes', 10, 3);
        
        // GraphQL hooks
        $this->loader->add_filter('graphql_cors_allowed_origins', $graphql, 'allowed_origins');
        $this->loader->add_filter('graphql_cors_allow_credentials', $graphql, 'allow_credentials');
        $this->loader->add_filter('graphql_cors_allowed_headers', $graphql, 'allowed_headers');
        $this->loader->add_filter('graphql_introspection_enabled', $graphql, 'enable_introspection');
        $this->loader->add_filter('graphql_tracing_enabled', $graphql, 'enable_tracing');
        $this->loader->add_filter('graphql_query_cache_enabled', $graphql, 'enable_caching');
        
        // Security hooks
        $this->loader->add_action('init', $security, 'block_theme_access');
        $this->loader->add_action('do_feed', $security, 'disable_feeds', 1);
        $this->loader->add_action('do_feed_rdf', $security, 'disable_feeds', 1);
        $this->loader->add_action('do_feed_rss', $security, 'disable_feeds', 1);
        $this->loader->add_action('do_feed_rss2', $security, 'disable_feeds', 1);
        $this->loader->add_action('do_feed_atom', $security, 'disable_feeds', 1);
        $this->loader->add_filter('wp_sitemaps_enabled', $security, 'disable_sitemaps');
        $this->loader->add_filter('comments_open', $security, 'disable_comments', 20, 2);
        $this->loader->add_filter('pings_open', $security, 'disable_comments', 20, 2);
    }

    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_version() {
        return $this->version;
    }

    public function get_loader() {
        return $this->loader;
    }
}