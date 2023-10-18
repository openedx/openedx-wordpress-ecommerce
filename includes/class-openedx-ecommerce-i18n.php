<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://edunext.co/
 * @since      1.0.0
 *
 * @package    Openedx_Ecommerce
 * @subpackage Openedx_Ecommerce/includes
 */

namespace App;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Openedx_Ecommerce
 * @subpackage Openedx_Ecommerce/includes
 * @author     eduNEXT <maria.magallanes@edunext.co>
 */
class Openedx_Ecommerce_I18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'openedx-woocommerce-plugin',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
