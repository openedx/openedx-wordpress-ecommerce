<?php
/**
 * Open edX WooCommerce Plugin utils.
 *
 * @package openedx-commerce
 * @since 1.6.0
 */

namespace OpenedX_Commerce\utils;

/**
 * Enrollment Request mode options.
 */
function get_enrollment_options() {
	return array(
		'honor'              => __( 'Honor', 'openedx-commerce' ),
		'audit'              => __( 'Audit', 'openedx-commerce' ),
		'verified'           => __( 'Verified', 'openedx-commerce' ),
		'credit'             => __( 'Credit', 'openedx-commerce' ),
		'professional'       => __( 'Professional', 'openedx-commerce' ),
		'no-id-professional' => __( 'No ID Professional', 'openedx-commerce' ),
	);
}
