<?php

/**
 * Plugin Name: Headless WordPress Admin
 * Plugin URI: https://benitoanagua.me
 * Description: Administración headless para WordPress con interfaz moderna y configuración completa
 * Version: 2.0.0
 * Author: Benito Anagua
 * Author URI: https://benitoanagua.me
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: headless-wp-admin
 * Domain Path: /languages
 * Requires at least: 6.0
 * Tested up to: 6.4
 * Requires PHP: 8.0
 * Network: false
 * 
 * @package HeadlessWPAdmin
 */

namespace HeadlessWPAdmin;

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes del plugin
define('HEADLESS_WP_ADMIN_VERSION', '2.0.0');
define('HEADLESS_WP_ADMIN_PLUGIN_FILE', __FILE__);
define('HEADLESS_WP_ADMIN_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('HEADLESS_WP_ADMIN_PLUGIN_URL', plugin_dir_url(__FILE__));
define('HEADLESS_WP_ADMIN_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Cargar Composer autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Inicializar el plugin
add_action('plugins_loaded', function () {
    Core\Plugin::getInstance();
});

// Hook de activación
register_activation_hook(__FILE__, [Core\Plugin::class, 'activate']);

// Hook de desactivación  
register_deactivation_hook(__FILE__, [Core\Plugin::class, 'deactivate']);

// Hook de desinstalación
register_uninstall_hook(__FILE__, [Core\Plugin::class, 'uninstall']);

// ================================
// FUNCIONES DE UTILIDAD GLOBALES
// ================================

/**
 * Función para verificar si el modo headless está activo
 */
function is_headless_mode_active()
{
    $settings = get_option('headless_wp_settings', []);
    return !empty($settings);
}

/**
 * Función para obtener una configuración específica
 */
function get_headless_setting($key, $default = null)
{
    $settings = get_option('headless_wp_settings', []);
    return isset($settings[$key]) ? $settings[$key] : $default;
}

/**
 * Función para verificar si GraphQL está habilitado
 */
function is_headless_graphql_enabled()
{
    return get_headless_setting('graphql_enabled', true);
}

/**
 * Función para verificar si REST API está habilitada
 */
function is_headless_rest_enabled()
{
    return get_headless_setting('rest_api_enabled', false);
}
