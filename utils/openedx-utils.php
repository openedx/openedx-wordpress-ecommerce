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
		'Honor'              => __( 'Honor', 'openedx-commerce' ),
		'Audit'              => __( 'Audit', 'openedx-commerce' ),
		'Verified'           => __( 'Verified', 'openedx-commerce' ),
		'Credit'             => __( 'Credit', 'openedx-commerce' ),
		'Professional'       => __( 'Professional', 'openedx-commerce' ),
		'No ID Professional' => __( 'No ID Professional', 'openedx-commerce' ),
	);
}
