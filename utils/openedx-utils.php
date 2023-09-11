<?php
/**
 * Open edX WooCommerce Plugin utils.
 *
 * @package openedx-woocommerce-plugin
 * @since 1.6.0
 */

namespace App\utils;

/**
 * Enrollment Request mode options.
 */
function get_enrollment_options() {
	return array(
		'Honor'              => __( 'Honor', 'wp-openedx-woocommerce-plugin' ),
		'Audit'              => __( 'Audit', 'wp-openedx-woocommerce-plugin' ),
		'Verified'           => __( 'Verified', 'wp-openedx-woocommerce-plugin' ),
		'Credit'             => __( 'Credit', 'wp-openedx-woocommerce-plugin' ),
		'Professional'       => __( 'Professional', 'wp-openedx-woocommerce-plugin' ),
		'No ID Professional' => __( 'No ID Professional', 'wp-openedx-woocommerce-plugin' ),
	);
}
