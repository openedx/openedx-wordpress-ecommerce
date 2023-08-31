<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://edunext.co/
 * @since      1.0.0
 *
 * @package    Openedx_Woocommerce_Plugin
 * @subpackage Openedx_Woocommerce_Plugin/public
 */

namespace App\public;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Openedx_Woocommerce_Plugin
 * @subpackage Openedx_Woocommerce_Plugin/public
 * @author     eduNEXT <maria.magallanes@edunext.co>
 */
class Openedx_Woocommerce_Plugin_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param string $plugin_name       The name of the plugin.
	 * @param string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Openedx_Woocommerce_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Openedx_Woocommerce_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/openedx-woocommerce-plugin-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Openedx_Woocommerce_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Openedx_Woocommerce_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/openedx-woocommerce-plugin-public.js', array( 'jquery' ), $this->version, false );
	}
}
