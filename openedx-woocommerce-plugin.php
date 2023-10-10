<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://edunext.co/
 * @since             1.0.0
 * @package           Openedx_Woocommerce_Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Open edX WooCommerce Plugin
 * Plugin URI:        https://github.com/eduNEXT/openedx-woocommerce-plugin
 * Description:       Easily connect your WooCommerce store to Open edX.
 * Version:           1.13.0
 * Author:            eduNEXT
 * Author URI:        https://edunext.co/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       openedx-woocommerce-plugin
 * Domain Path:       /languages
 */

use App\Openedx_Woocommerce_Plugin_Activator;
use App\Openedx_Woocommerce_Plugin_Deactivator;
use App\Openedx_Woocommerce_Plugin;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'OPENEDX_WOOCOMMERCE_PLUGIN_VERSION', '1.13.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-openedx-woocommerce-plugin-activator.php
 */
function activate_openedx_woocommerce_plugin() {
	include_once plugin_dir_path( __FILE__ ) . 'includes/class-openedx-woocommerce-plugin-activator.php';
	Openedx_Woocommerce_Plugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-openedx-woocommerce-plugin-deactivator.php
 */
function deactivate_openedx_woocommerce_plugin() {
	include_once plugin_dir_path( __FILE__ ) . 'includes/class-openedx-woocommerce-plugin-deactivator.php';
	Openedx_Woocommerce_Plugin_Deactivator::deactivate();
}

/**
 * Create the table for the logs on plugin activation
 */
function create_enrollment_logs_table() {
	global $wpdb;
	$logs_table = wp_cache_get( 'enrollment_logs_req_table', 'db' );

	if ( ! $logs_table ) {
		if ( $wpdb->get_var( 'SHOW TABLES LIKE enrollment_logs_req_table' ) !== $logs_table ) { // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $logs_table (
				id INT NOT NULL AUTO_INCREMENT,
				post_id INT NOT NULL,
				mod_date DATETIME NOT NULL,
				user VARCHAR(255) NOT NULL,
				action_name VARCHAR(255) NOT NULL,
				object_before LONGTEXT NOT NULL,
				object_after LONGTEXT NOT NULL,
				object_status VARCHAR(255) NOT NULL,
				api_response MEDIUMTEXT NOT NULL,
				PRIMARY KEY (id)
			) $charset_collate;";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			dbDelta( $sql );
		}
		wp_cache_set( 'enrollment_logs_req_table', $logs_table, 'db', 3600 );
	}
}

register_activation_hook( __FILE__, 'activate_openedx_woocommerce_plugin' );
register_activation_hook( __FILE__, 'create_enrollment_logs_table' );
register_deactivation_hook( __FILE__, 'deactivate_openedx_woocommerce_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-openedx-woocommerce-plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_openedx_woocommerce_plugin() {

	$plugin = new Openedx_Woocommerce_Plugin();
	$plugin->run();
}
run_openedx_woocommerce_plugin();
