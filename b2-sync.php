<?php
/**
 * b2-sync
 * namespace used: B2Sync
 * (to prevent any clashes with other plugins)
 *
 * @author Wasseem Khayrattee
 * @copyright 2021 Wasseem Khayrattee
 * @license GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: b2-sync
 * Plugin URI: https://github.com/wkhayrattee/b2-sync
 * Description: A WordPress plugin to sync assets files from wp-content/uploads onto a Backblaze B2 bucket
 * Version: 1.2.0
 * Requires at least: 5.7.0
 * Requires PHP: 7.2
 * Author: Wasseem Khayrattee
 * Author URI: https://github.com/wkhayrattee/
 * License: GPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: b2-sync
 * Domain Path: /languages
 *
 *
 * reference: https://developer.wordpress.org/plugins/
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 */

/**
 * Make sure we don't expose any info if called directly
 */
if (!function_exists('add_action')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit;
}

/**
 * Some global constants for our use-case
 */
define('B2Sync_DS', DIRECTORY_SEPARATOR);
define('B2Sync_VERSION', '1.2.0');
define('B2Sync_MINIMUM_WP_VERSION', '5.7');
define('B2Sync_PLUGIN_DIR_URL', plugin_dir_url(__FILE__)); //has trailing slash at end
define('B2Sync_PLUGIN_DIR', plugin_dir_path(__FILE__)); //has trailing slash at end
define('B2Sync_BASENAME', plugin_basename(B2Sync_PLUGIN_DIR));
define('B2Sync_PLUGIN_VIEWS', B2Sync_PLUGIN_DIR . 'views' . B2Sync_DS);
define('B2Sync_PLUGIN_CACHE_DIR', WP_CONTENT_DIR . B2Sync_DS . 'cache' . B2Sync_DS);

/**
 * load our main file now with composer autoloading
 */
require_once B2Sync_PLUGIN_DIR . B2Sync_DS . 'includes/vendor/autoload.php';

/**
 * Register main Hooks
 */
register_activation_hook(__FILE__, ['B2Sync\\PluginClass', 'plugin_activation']);
register_deactivation_hook(__FILE__, ['B2Sync\\PluginClass', 'plugin_deactivation']);
//the below will be handled by uninstall.php
//register_uninstall_hook(__FILE__, ['B2Sync\\PluginClass', 'plugin_uninstall']);

/**
 * Load the admin page interface
 */
if (is_admin()) {
    add_action('init', ['B2Sync\\PluginClass', 'adminInit']);
}

/**
 * Start main process
 */
\B2Sync\PluginClass::plugin_start();
