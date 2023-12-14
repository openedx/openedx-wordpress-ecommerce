<?php
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
 * @package    Openedx_Commerce
 * @subpackage Openedx_Commerce/includes
 * @author     eduNEXT <maria.magallanes@edunext.co>
 */

namespace OpenedXCommerce;

use OpenedXCommerce\admin\Openedx_Commerce_Admin;
use OpenedXCommerce\public\Openedx_Commerce_Public;
use OpenedXCommerce\admin\views\Openedx_Commerce_Settings;
use OpenedXCommerce\model\Openedx_Commerce_Enrollment;

/**
 * This class contains the function to register a new custom post type.
 */
class Openedx_Commerce {


	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Openedx_Commerce_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		if ( defined( 'OPENEDX_COMMERCE_VERSION' ) ) {
			$this->version = OPENEDX_COMMERCE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'openedx-commerce';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_plugin_settings_hooks();
		$this->define_enqueue_scripts();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Openedx_Commerce_Loader. Orchestrates the hooks of the plugin.
	 * - Openedx_Commerce_I18n. Defines internationalization functionality.
	 * - Openedx_Commerce_Admin. Defines all hooks for the admin area.
	 * - Openedx_Commerce_Public. Defines all hooks for the public side of the site.
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
		include_once plugin_dir_path( __DIR__ )
			. 'includes/class-openedx-commerce-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		include_once plugin_dir_path( __DIR__ )
			. 'includes/class-openedx-commerce-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		include_once plugin_dir_path( __DIR__ )
			. 'admin/class-openedx-commerce-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		include_once plugin_dir_path( __DIR__ )
			. 'public/class-openedx-commerce-public.php';

		$this->loader = new Openedx_Commerce_Loader();

		/**
		 * The class responsible for defining the enrollment object
		 */
		include_once plugin_dir_path( __DIR__ )
			. 'includes/model/class-openedx-commerce-enrollment.php';

		/**
		 * The class responsible for defining the custom-post-type object
		 */
		include_once plugin_dir_path( __DIR__ )
			. 'includes/model/class-openedx-commerce-post-type.php';

		include_once plugin_dir_path( __DIR__ )
			. 'includes/model/class-openedx-commerce-log.php';

		/**
		 * The class responsible for rendering the enrollment info form
		 */
		include_once plugin_dir_path( __DIR__ )
			. 'admin/views/class-openedx-commerce-enrollment-info-form.php';

		/**
		 * The file that contains variables and functions used repeatedly in the plugin.
		 */
		include_once plugin_dir_path( __DIR__ )
			. 'utils/openedx-utils.php';

		/**
		 * The file that contains variables and functions used repeatedly in the plugin.
		 */
		include_once plugin_dir_path( __DIR__ )
			. 'admin/views/class-openedx-commerce-settings.php';

		/**
		 * Includes the Openedx_Commerce_Api_Calls model class file.
		 *
		 * This includes the file defining the Openedx_Commerce_Api_Calls class
		 * which handles making API requests to the Open edX platform.
		 *
		 * The path is relative to the main plugin file directory.
		 */
		include_once plugin_dir_path( __DIR__ )
			. 'includes/model/class-openedx-commerce-api-calls.php';

		include_once plugin_dir_path( __DIR__ )
			. 'test/class-enrollment-test.php';
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Openedx_Commerce_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Openedx_Commerce_I18n();

		$this->loader->add_action(
			'plugins_loaded',
			$plugin_i18n,
			'load_plugin_textdomain'
		);
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Openedx_Commerce_Admin(
			$this->get_plugin_name(),
			$this->get_version()
		);

		// Register enrollment request custom-post-type.
		$this->loader->add_action(
			'init',
			$plugin_admin,
			'register_enrollment_custom_post_type'
		);

		// Render enrollment request info form.
		$this->loader->add_action( 'edit_form_after_title', $plugin_admin, 'render_enrollment_info_form' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_filter( 'gettext', $this, 'openedx_plugin_custom_post_message', 10, 3 );
		$this->loader->wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . '../admin/css/class-openedx-commerce-admin.css',
			array(),
			$this->version,
			'all'
		);

		// Redirection from enrollment to order and enrollment to order.
		$this->loader->add_filter( 'woocommerce_admin_order_item_headers', $plugin_admin, 'add_custom_column_order_items' );
		$this->loader->add_action( 'woocommerce_admin_order_item_values', $plugin_admin, 'add_admin_order_item_values', 10, 3 );
		$this->loader->add_action( 'save_post_shop_order', $plugin_admin, 'save_order_meta_data' );
		$this->loader->add_action(
			'woocommerce_product_options_general_product_data',
			$plugin_admin,
			'add_custom_product_fields'
		);
		$this->loader->add_action(
			'woocommerce_process_product_meta',
			$plugin_admin,
			'save_custom_product_fields'
		);

		$this->loader->add_action( 'woocommerce_order_status_changed', $plugin_admin, 'process_order_data', 10, 2 );
		$this->loader->add_action( 'woocommerce_order_refunded', $plugin_admin, 'unenroll_course_refund', 10, 2 );
		$this->loader->add_filter( 'product_type_options', $plugin_admin, 'add_openedx_course_product_type' );
		$this->loader->add_action( 'woocommerce_update_product', $plugin_admin, 'save_openedx_option' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Openedx_Commerce_Public(
			$this->get_plugin_name(),
			$this->get_version()
		);

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	/**
	 * Register all the hooks related to custom scripts for specific functionalities.
	 *
	 * @since    1.11.0
	 * @access   private
	 */
	private function define_enqueue_scripts() {
		wp_register_script( 'product-type-script', plugin_dir_url( __FILE__ ) . '../admin/js/product-type.js', array(), $this->get_version(), true );
		wp_enqueue_script( 'product-type-script' );

		wp_register_script( 'course-id-restriction-script', plugin_dir_url( __FILE__ ) . '../admin/js/course-id-restriction.js', array(), $this->get_version(), true );
		wp_enqueue_script( 'course-id-restriction-script' );
	}

	/**
	 * Define the plugin settings hooks.
	 *
	 * Initializes the Openedx_Commerce_Settings class
	 * and registers its admin menu and settings hooks using the loader.
	 *
	 * @return void
	 */
	private function define_plugin_settings_hooks() {

		$plugin_settings = new Openedx_Commerce_Settings();

		$this->loader->add_action( 'admin_menu', $plugin_settings, 'openedx_settings_submenu' );
		$this->loader->add_action( 'admin_init', $plugin_settings, 'openedx_settings_init' );
	}

	/**
	 * Modify the message displayed when a custom-post-type is updated
	 *
	 * @param string $translated_text translation text.
	 * @param string $text text to be translated.
	 * @param string $domain text domain.
	 * @return string $translated_text post updated message.
	 */
	public function openedx_plugin_custom_post_message( $translated_text, $text, $domain ) {

		if ( 'default' === $domain && 'Post updated.' === $text ) {
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
	 * @return    Openedx_Commerce_Loader    Orchestrates the hooks of the plugin.
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
