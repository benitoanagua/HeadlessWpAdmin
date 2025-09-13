<?php

/**
 * Bootstrap file for PHPStan
 * Define WordPress constants and functions for static analysis
 */

// Define ABSPATH if not already defined
if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/wordpress/');
}

// Define common WordPress constants
$wpConstants = [
    'WP_DEBUG',
    'WP_CONTENT_DIR',
    'WP_CONTENT_URL',
    'WP_PLUGIN_DIR',
    'WP_PLUGIN_URL',
    'WPLANG',
    'WPINC',
    'TEMPLATEPATH',
    'STYLESHEETPATH',
    'DB_HOST',
    'DB_NAME',
    'DB_USER',
    'DB_PASSWORD',
    'DB_CHARSET',
    'DB_COLLATE',
    'AUTH_KEY',
    'SECURE_AUTH_KEY',
    'LOGGED_IN_KEY',
    'NONCE_KEY',
    'AUTH_SALT',
    'SECURE_AUTH_SALT',
    'LOGGED_IN_SALT',
    'NONCE_SALT',
    'DOING_AJAX',
    'DOING_CRON',
    'WP_CLI',
    'REST_REQUEST'
];

foreach ($wpConstants as $constant) {
    if (!defined($constant)) {
        define($constant, '');
    }
}

// Define some basic WordPress functions if they don't exist
if (!function_exists('plugin_dir_path')) {
    function plugin_dir_path($file)
    {
        return dirname($file) . '/';
    }
}

if (!function_exists('plugin_dir_url')) {
    function plugin_dir_url($file)
    {
        return 'http://example.com/wp-content/plugins/' . basename(dirname($file)) . '/';
    }
}

if (!function_exists('plugin_basename')) {
    function plugin_basename($file)
    {
        return basename(dirname($file)) . '/' . basename($file);
    }
}

if (!function_exists('load_plugin_textdomain')) {
    function load_plugin_textdomain($domain, $deprecated = false, $rel_path = false)
    {
        return true;
    }
}

if (!function_exists('add_action')) {
    function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1)
    {
        return true;
    }
}

if (!function_exists('add_filter')) {
    function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1)
    {
        return true;
    }
}

if (!function_exists('is_admin')) {
    function is_admin()
    {
        return false;
    }
}

if (!function_exists('wp_enqueue_script')) {
    function wp_enqueue_script($handle, $src = '', $deps = [], $ver = false, $in_footer = false)
    {
        return true;
    }
}

if (!function_exists('wp_enqueue_style')) {
    function wp_enqueue_style($handle, $src = '', $deps = [], $ver = false, $media = 'all')
    {
        return true;
    }
}

if (!function_exists('flush_rewrite_rules')) {
    function flush_rewrite_rules($hard = true)
    {
        return true;
    }
}

if (!function_exists('register_activation_hook')) {
    function register_activation_hook($file, $function)
    {
        return true;
    }
}

if (!function_exists('register_deactivation_hook')) {
    function register_deactivation_hook($file, $function)
    {
        return true;
    }
}

if (!function_exists('register_uninstall_hook')) {
    function register_uninstall_hook($file, $function)
    {
        return true;
    }
}

if (!function_exists('wp_remote_get')) {
    function wp_remote_get($url, $args = [])
    {
        return new \WP_Error('not_implemented', 'Function not implemented in PHPStan bootstrap');
    }
}

if (!function_exists('is_wp_error')) {
    function is_wp_error($thing)
    {
        return false;
    }
}

if (!function_exists('wp_send_json_success')) {
    function wp_send_json_success($data = null)
    {
        return true;
    }
}

if (!function_exists('wp_send_json_error')) {
    function wp_send_json_error($data = null, $status_code = null)
    {
        return true;
    }
}

if (!function_exists('wp_create_nonce')) {
    function wp_create_nonce($action = '')
    {
        return 'test_nonce';
    }
}

if (!function_exists('wp_verify_nonce')) {
    function wp_verify_nonce($nonce, $action = '')
    {
        return true;
    }
}

if (!function_exists('current_user_can')) {
    function current_user_can($capability, ...$args)
    {
        return true;
    }
}

if (!function_exists('wp_get_current_user')) {
    function wp_get_current_user()
    {
        return (object) [
            'ID' => 1,
            'user_login' => 'admin',
            'user_email' => 'admin@example.com',
            'user_registered' => '2023-01-01 00:00:00',
            'user_status' => 0,
            'display_name' => 'Admin'
        ];
    }
}

if (!function_exists('get_current_screen')) {
    function get_current_screen()
    {
        return null;
    }
}

if (!function_exists('add_menu_page')) {
    function add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function = '', $icon_url = '', $position = null)
    {
        return true;
    }
}

if (!function_exists('add_submenu_page')) {
    function add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function = '', $position = null)
    {
        return true;
    }
}

if (!function_exists('register_setting')) {
    function register_setting($option_group, $option_name, $args = [])
    {
        return true;
    }
}

if (!function_exists('settings_fields')) {
    function settings_fields($option_group)
    {
        return '';
    }
}

if (!function_exists('do_settings_sections')) {
    function do_settings_sections($page)
    {
        return '';
    }
}

if (!function_exists('add_settings_section')) {
    function add_settings_section($id, $title, $callback, $page)
    {
        return true;
    }
}

if (!function_exists('add_settings_field')) {
    function add_settings_field($id, $title, $callback, $page, $section = 'default', $args = [])
    {
        return true;
    }
}

if (!function_exists('submit_button')) {
    function submit_button($text = null, $type = 'primary', $name = 'submit', $wrap = true, $other_attributes = null)
    {
        return '<button type="submit">Submit</button>';
    }
}

if (!function_exists('wp_nonce_field')) {
    function wp_nonce_field($action = -1, $name = '_wpnonce', $referer = true, $echo = true)
    {
        return '<input type="hidden" name="' . $name . '" value="test_nonce">';
    }
}

if (!function_exists('checked')) {
    function checked($checked, $current = true, $echo = true)
    {
        return $checked ? 'checked="checked"' : '';
    }
}

if (!function_exists('selected')) {
    function selected($selected, $current = true, $echo = true)
    {
        return $selected ? 'selected="selected"' : '';
    }
}

if (!function_exists('disabled')) {
    function disabled($disabled, $current = true, $echo = true)
    {
        return $disabled ? 'disabled="disabled"' : '';
    }
}

if (!function_exists('esc_attr')) {
    function esc_attr($text)
    {
        return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_html')) {
    function esc_html($text)
    {
        return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_url')) {
    function esc_url($url, $protocols = null, $_context = 'display')
    {
        return filter_var($url, FILTER_SANITIZE_URL);
    }
}

if (!function_exists('esc_textarea')) {
    function esc_textarea($text)
    {
        return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('wp_strip_all_tags')) {
    function wp_strip_all_tags($string, $remove_breaks = false)
    {
        return strip_tags($string);
    }
}

if (!function_exists('wp_autop')) {
    function wp_autop($pee, $br = true)
    {
        return $pee;
    }
}

if (!function_exists('wp_kses_post')) {
    function wp_kses_post($data)
    {
        return $data;
    }
}

if (!function_exists('home_url')) {
    function home_url($path = '', $scheme = null)
    {
        return 'http://example.com/' . ltrim($path, '/');
    }
}

if (!function_exists('admin_url')) {
    function admin_url($path = '', $scheme = 'admin')
    {
        return 'http://example.com/wp-admin/' . ltrim($path, '/');
    }
}

if (!function_exists('get_locale')) {
    function get_locale()
    {
        return 'en_US';
    }
}

if (!function_exists('get_bloginfo')) {
    function get_bloginfo($show = '', $filter = 'raw')
    {
        switch ($show) {
            case 'version':
                return '6.4';
            case 'name':
                return 'Test Site';
            case 'description':
                return 'Just another WordPress site';
            default:
                return '';
        }
    }
}

if (!function_exists('shortcode_atts')) {
    function shortcode_atts($pairs, $atts, $shortcode = '')
    {
        return array_merge($pairs, (array) $atts);
    }
}

if (!function_exists('add_shortcode')) {
    function add_shortcode($tag, $callback)
    {
        return true;
    }
}

if (!function_exists('wp_die')) {
    function wp_die($message = '', $title = '', $args = [])
    {
        exit;
    }
}

if (!function_exists('nocache_headers')) {
    function nocache_headers()
    {
        return true;
    }
}

if (!function_exists('wp_redirect')) {
    function wp_redirect($location, $status = 302, $x_redirect_by = 'WordPress')
    {
        return true;
    }
}

if (!function_exists('add_query_arg')) {
    function add_query_arg($args, $url = null)
    {
        return $url ? $url . '?' . http_build_query($args) : http_build_query($args);
    }
}

// Mock WP_Error class if it doesn't exist
if (!class_exists('WP_Error')) {
    class WP_Error
    {
        public $errors = [];
        public $error_data = [];

        public function __construct($code = '', $message = '', $data = '')
        {
            if (!empty($code)) {
                $this->errors[$code][] = $message;
            }
            if (!empty($data)) {
                $this->error_data[$code] = $data;
            }
        }

        public function get_error_message($code = '')
        {
            if (empty($code)) {
                $code = $this->get_error_code();
            }
            if (isset($this->errors[$code])) {
                return $this->errors[$code][0];
            }
            return '';
        }

        public function get_error_code()
        {
            if (empty($this->errors)) {
                return '';
            }
            $codes = array_keys($this->errors);
            return $codes[0];
        }
    }
}

// Mock WP_Screen class if it doesn't exist
if (!class_exists('WP_Screen')) {
    class WP_Screen
    {
        public $id;
        public $base;
        public $action;
        public $parent_base;
        public $parent_file;
        public $post_type;
        public $taxonomy;

        public function __construct()
        {
            $this->id = 'toplevel_page_headless-mode';
        }
    }
}

// Mock WP_Admin_Bar class if it doesn't exist
if (!class_exists('WP_Admin_Bar')) {
    class WP_Admin_Bar
    {
        public $nodes = [];

        public function add_node($args)
        {
            $this->nodes[] = $args;
            return true;
        }

        public function remove_node($id)
        {
            foreach ($this->nodes as $key => $node) {
                if ($node['id'] === $id) {
                    unset($this->nodes[$key]);
                    return true;
                }
            }
            return false;
        }
    }
}
