<?php
/**
 * Plugin Name:       Open edX Commerce
 * Plugin URI:        https://github.com/openedx/openedx-wordpress-ecommerce
 * Description:       Easily connect your WooCommerce store to Open edX.
 * Version:           2.0.3
 * Author:            The Open edX Community
 * Author URI:        https://openedx.org/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       openedx-commerce
 * Domain Path:       /languages
 * Requires at least: 6.3
 * Requires PHP: 8.0
 *
 * @package           OpenedX_Commerce
 *
 * @wordpress-plugin
 */

use OpenedX_Commerce\Openedx_Commerce_Activator;
use OpenedX_Commerce\Openedx_Commerce_Deactivator;
use OpenedX_Commerce\Openedx_Commerce;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'OPENEDX_COMMERCE_VERSION', '2.0.3' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-openedx-commerce-activator.php
 */
function openedx_commerce_plugin_activate() {
	include_once plugin_dir_path( __FILE__ ) . 'includes/class-openedx-commerce-activator.php';
	Openedx_Commerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-openedx-commerce-deactivator.php
 */
function openedx_commerce_plugin_deactivate() {
	include_once plugin_dir_path( __FILE__ ) . 'includes/class-openedx-commerce-deactivator.php';
	Openedx_Commerce_Deactivator::deactivate();
}

/**
 * Create the table for the logs on plugin activation
 */
function openedx_commerce_create_enrollment_logs_table() {
	global $wpdb;
	$logs_table      = wp_cache_get( 'enrollment_logs_req_table', 'db' );
	$logs_table_name = $wpdb->prefix . 'enrollment_logs_req_table';

	if ( ! $logs_table ) {
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $logs_table ) ) !== $logs_table ) { // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $logs_table_name (
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

register_activation_hook( __FILE__, 'openedx_commerce_plugin_activate' );
register_activation_hook( __FILE__, 'openedx_commerce_create_enrollment_logs_table' );
register_deactivation_hook( __FILE__, 'openedx_commerce_plugin_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-openedx-commerce.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function openedx_commerce_plugin_run() {

	$plugin = new Openedx_Commerce();
	$plugin->run();
}
openedx_commerce_plugin_run();
