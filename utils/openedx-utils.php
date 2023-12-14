<?php
/**
 * Open edX WooCommerce Plugin utils.
 *
 * @package openedx-commerce
 * @since 1.6.0
 */

namespace OpenedXCommerce\utils;

/**
 * Enrollment Request mode options.
 */
function get_enrollment_options() {
	return array(
		'Honor'              => __( 'Honor', 'wp-openedx-commerce' ),
		'Audit'              => __( 'Audit', 'wp-openedx-commerce' ),
		'Verified'           => __( 'Verified', 'wp-openedx-commerce' ),
		'Credit'             => __( 'Credit', 'wp-openedx-commerce' ),
		'Professional'       => __( 'Professional', 'wp-openedx-commerce' ),
		'No ID Professional' => __( 'No ID Professional', 'wp-openedx-commerce' ),
	);
}
