<?php

namespace App;
use App\admin\Openedx_Woocommerce_Plugin_Admin;
use App\public\Openedx_Woocommerce_Plugin_Public;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://edunext.co/
 * @since      1.0.0
 *
 * @package    Openedx_Woocommerce_Plugin
 * @subpackage Openedx_Woocommerce_Plugin/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Openedx_Woocommerce_Plugin
 * @subpackage Openedx_Woocommerce_Plugin/includes
 * @author     eduNEXT <maria.magallanes@edunext.co>
 */
class Openedx_Woocommerce_Plugin {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Openedx_Woocommerce_Plugin_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'OPENEDX_WOOCOMMERCE_PLUGIN_VERSION' ) ) {
			$this->version = OPENEDX_WOOCOMMERCE_PLUGIN_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'openedx-woocommerce-plugin';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Openedx_Woocommerce_Plugin_Loader. Orchestrates the hooks of the plugin.
	 * - Openedx_Woocommerce_Plugin_i18n. Defines internationalization functionality.
	 * - Openedx_Woocommerce_Plugin_Admin. Defines all hooks for the admin area.
	 * - Openedx_Woocommerce_Plugin_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
         include_once plugin_dir_path( dirname(__FILE__)) . 'includes/Openedx_Woocommerce_Plugin_Loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
         include_once plugin_dir_path( dirname(__FILE__)) . 'includes/Openedx_Woocommerce_Plugin_i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
         include_once plugin_dir_path( dirname(__FILE__)) . 'admin/Openedx_Woocommerce_Plugin_Admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
         include_once plugin_dir_path( dirname(__FILE__)) . 'public/Openedx_Woocommerce_Plugin_Public.php';

		$this->loader = new Openedx_Woocommerce_Plugin_Loader();

		/**
		 * The class responsible for defining the enrollment object
		 */
         include_once plugin_dir_path( dirname(__FILE__)) . 'includes/model/Openedx_Woocommerce_Plugin_Enrollment.php';

		/**
		 * The class responsible for defining the custom-post-type object
		 */
         include_once plugin_dir_path( dirname(__FILE__)) . 'includes/model/Openedx_Woocommerce_Plugin_Post_Type.php';

		/**
		 * The class responsible for rendering the enrollment info form
		 */
         include_once plugin_dir_path( dirname(__FILE__)) . 'admin/views/Openedx_Woocommerce_Plugin_Enrollment_Info_Form.php';
         
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Openedx_Woocommerce_Plugin_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Openedx_Woocommerce_Plugin_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Openedx_Woocommerce_Plugin_Admin( $this->get_plugin_name(), $this->get_version() );

		// Register enrollment request custom-post-type
		$this->loader->add_action( 'init', $plugin_admin, 'register_enrollment_custom_post_type' );

		// Render enrollment request info form
		$this->loader->add_action( 'edit_form_after_title', $plugin_admin, 'render_enrollment_info_form' );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_filter( 'gettext', $this, 'openedx_plugin_custom_post_message', 10, 3 );
		$this->loader->add_action('woocommerce_product_options_general_product_data', $plugin_admin, 'add_custom_product_fields');
		$this->loader->add_action('woocommerce_process_product_meta', $plugin_admin, 'save_custom_product_fields');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Openedx_Woocommerce_Plugin_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

     /**
     * Modify the message displayed when a custom-post-type is updated
     * 
     * @param string $translated_text translation text
     * @param string $text text to be translated
     * @param string $domain text domain
     * @return string $translated_text post updated message
     */
	function openedx_plugin_custom_post_message( $translated_text, $text, $domain ) {

		if ( $domain === 'default' && $text === 'Post updated.' ) {
			$translated_text = 'Enrollment action executed';
		}
		return $translated_text;
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Openedx_Woocommerce_Plugin_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
