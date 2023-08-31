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
		'Honor'              => __( 'Honor', 'wp-edunext-marketing-site' ),
		'Audit'              => __( 'Audit', 'wp-edunext-marketing-site' ),
		'Verified'           => __( 'Verified', 'wp-edunext-marketing-site' ),
		'Credit'             => __( 'Credit', 'wp-edunext-marketing-site' ),
		'Professional'       => __( 'Professional', 'wp-edunext-marketing-site' ),
		'No ID Professional' => __( 'No ID Professional', 'wp-edunext-marketing-site' ),
	);
}
