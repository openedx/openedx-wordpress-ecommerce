<?php
/**
 * The main file of the plugin, it contains the plugin class
 * and manage every step in a custom post type enrollment registration,
 * modification, deletion and sync.
 *
 * @category   Model
 * @package    WordPress
 * @subpackage Openedx_Woocommerce_Plugin
 * @since      1.0.0
 */

namespace App\model;

use App\model\Openedx_Woocommerce_Plugin_Log;
use App\Openedx_Woocommerce_Plugin;
use App\admin\Openedx_Woocommerce_Plugin_Admin;
use App\model\Openedx_Woocommerce_Plugin_Api_Calls;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This class contains every step to process the enrollment post type operations.
 */
class Openedx_Woocommerce_Plugin_Enrollment {


	/**
	 * The name for the Open edX enrollment custom post type.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.9.0
	 */
	public $post_type = 'openedx_enrollment';

	/**
	 * The variable to store the log manager object.
	 *
	 * @var     object
	 * @access  private
	 * @since   1.1.1
	 */

	private $log_manager;

	/**
	 * Constructor function.
	 *
	 * @param   object $parent_name The parent class.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct( $parent_name ) {
		$this->parent = $parent_name;

		// Register all the model related hooks and log manager.
		$this->register_hook_callbacks();
		$this->register_log_manager();
	}

	/**
	 * Register the log manager object to the variable.
	 *
	 * @return void
	 */
	public function register_log_manager() {
		$this->log_manager = new Openedx_Woocommerce_Plugin_Log();
	}

	/**
	 * Register all of the hooks related to the model functionality.
	 *
	 * @return void
	 */
	protected function register_hook_callbacks() {
		/**
		 * If you think all model related add_actions & filters should be in
		 * the model class only, then this this the place where you can place
		 * them.
		 *
		 * You can remove this method if you are not going to use it.
		 */

		// Add types of status to the enrollment request custom-post-type.
		add_action( 'init', array( $this, 'register_status' ), 10, 3 );

		// Add the enrollment logic to the save post hook.
		add_action( 'save_post', array( $this, 'save_action' ), 10, 3 );
	}

	/**
	 * Register the custom post type for the enrollment requests.
	 *
	 * @return void
	 */
	public function register_enrollment_custom_post_type() {
		// Add the custom post type.
		$enrollment_cpt_options = array(
			'public'            => false,
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_in_nav_menus' => true,
			'supports'          => array( '' ),
			'menu_icon'         => 'dashicons-admin-post',
			'labels'            => array(
				'name'          => 'Open edX Enrollment Requests',
				'singular_name' => 'Open edX Enrollment Request',
				'menu_name'     => 'Open edX Sync',
				'all_items'     => 'Enrollments Manager',
				'add_new_item'  => 'Add New Enrollment Request',
				'edit_item'     => 'Edit Enrollment Request',
			),
		);

		// Register post-type using wrapper custom-post-type function.
		$this->parent->register_post_type( 'openedx_enrollment', ' ', ' ', '', $enrollment_cpt_options );
	}

	/**
	 * Unregister the save hook to prevent an infinite cycle of hook recursion
	 *
	 * @return void
	 */
	public function unregister_save_hook() {
		remove_action( 'save_post', array( $this, 'save_action' ), 10, 3 );
	}

	/**
	 * Register the save hook to prevent an infinite cycle of hook recursion
	 *
	 * @return void
	 */
	public function register_save_hook() {
		add_action( 'save_post', array( $this, 'save_action' ), 10, 3 );
	}

	/**
	 * Creates specific status for the post type
	 *
	 * @return  void
	 */
	public function register_status() {
		register_post_status(
			'enrollment-success',
			array(
				'label'                     => __( 'Success', 'wp-openedx-woocommerce-plugin' ),
				'public'                    => false,
				'internal'                  => true,
				'private'                   => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				// translators: %s: number of items.
				'label_count'               => _n_noop( 'Success <span class="count">(%s)</span>', 'Success <span class="count">(%s)</span>', 'wp-openedx-woocommerce-plugin' ),
			)
		);
		register_post_status(
			'enrollment-pending',
			array(
				'label'                     => __( 'Pending', 'wp-openedx-woocommerce-plugin' ),
				'public'                    => false,
				'internal'                  => true,
				'private'                   => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				// translators: %s: number of items.
				'label_count'               => _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>', 'wp-openedx-woocommerce-plugin' ),
			)
		);
		register_post_status(
			'enrollment-error',
			array(
				'label'                     => __( 'Error', 'wp-openedx-woocommerce-plugin' ),
				'public'                    => false,
				'internal'                  => true,
				'private'                   => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				// translators: %s: number of items.
				'label_count'               => _n_noop( 'Error <span class="count">(%s)</span>', 'Error <span class="count">(%s)</span>', 'wp-openedx-woocommerce-plugin' ),
			)
		);
	}

	/**
	 * Wrapper for the WP function that prevents an infinite cycle of hook recursion
	 *
	 * @param array $post The post info in an array.
	 */
	public function wp_update_post( $post ) {
		$this->unregister_save_hook();

		wp_update_post( $post );

		$this->register_save_hook();
	}

	/**
	 * Save post metadata when a post is saved.
	 *
	 * @param int  $post_id The post ID.
	 * @param post $post The post object.
	 */
	public function save_action( $post_id, $post ) {

		if ( $this->post_type !== $post->post_type ) {
			return;
		}

		$enrollment_arr = array();

		if ( isset( $_POST['enrollment_course_id'] ) ) {
			$enrollment_arr['enrollment_course_id'] = sanitize_text_field( wp_unslash( $_POST['enrollment_course_id'] ) );
		} else {
			$enrollment_arr['enrollment_course_id'] = sanitize_text_field( wp_unslash( '' ) );
		}

		if ( isset( $_POST['enrollment_email'] ) ) {
			$enrollment_arr['enrollment_email'] = sanitize_text_field( wp_unslash( $_POST['enrollment_email'] ) );
		} else {
			$enrollment_arr['enrollment_email'] = sanitize_text_field( wp_unslash( '' ) );
		}

		if ( isset( $_POST['enrollment_mode'] ) ) {
			$enrollment_arr['enrollment_mode'] = sanitize_text_field( wp_unslash( $_POST['enrollment_mode'] ) );
		} else {
			$enrollment_arr['enrollment_mode'] = sanitize_text_field( wp_unslash( '' ) );
		}

		if ( isset( $_POST['enrollment_request_type'] ) ) {
			$enrollment_arr['enrollment_request_type'] = sanitize_text_field( wp_unslash( $_POST['enrollment_request_type'] ) );
		} else {
			$enrollment_arr['enrollment_request_type'] = sanitize_text_field( wp_unslash( '' ) );
		}

		if ( isset( $_POST['enrollment_order_id'] ) ) {
			$enrollment_arr['enrollment_order_id'] = sanitize_text_field( wp_unslash( $_POST['enrollment_order_id'] ) );
		} else {
			$enrollment_arr['enrollment_order_id'] = sanitize_text_field( wp_unslash( '' ) );
		}

		if ( isset( $_POST['enrollment_process'] ) ) {

			if ( isset( $_POST['enrollment_force'] ) && isset( $_POST['enrollment_allowed'] ) ) {
				$enrollment_action = 'enrollment_allowed_force';
			} elseif ( isset( $_POST['enrollment_force'] ) ) {
				$enrollment_action = 'enrollment_force';
			} elseif ( isset( $_POST['enrollment_allowed'] ) ) {
				$enrollment_action = 'enrollment_allowed';
			} else {
				$enrollment_action = 'enrollment_process';
			}
		}

		if ( isset( $_POST['save_no_process'] ) ) {
			$enrollment_action = 'save_no_process';
		}

		if ( isset( $_POST['enrollment_sync'] ) ) {
			$enrollment_action = 'enrollment_sync';
		}

		$this->save_enrollment( $post, $enrollment_arr, $enrollment_action );
	}

	/**
	 * Creates a new post in the database and runs it through the save.
	 *
	 * @param array  $enrollment_arr An array containing the enrollment info.
	 * @param string $enrollment_action The API action to perform once the wp process is done.
	 * @param int    $order_id The order ID in case the enrollment is created from an order.
	 *
	 * @return object $post The post object.
	 */
	public function insert_new( $enrollment_arr, $enrollment_action = '', $order_id = null ) {
		$this->unregister_save_hook();

		$new_enrollment = array(
			'post_content' => 'Created automatically by woocommerce to fullfill an order.',
			'post_type'    => 'openedx_enrollment',
		);
		$post_id        = wp_insert_post( $new_enrollment );
		$post           = get_post( $post_id );

		$this->save_enrollment( $post, $enrollment_arr, $enrollment_action, $order_id );
		return $post;
	}

	/**
	 * Save openedx request based on the incomming args.
	 *
	 * @param post   $post The post object.
	 * @param array  $enrollment_arr An array containing the enrollment info.
	 * @param string $enrollment_action The API action to perform once the wp process is done.
	 * @param int    $order_id The order ID in case the enrollment is created from an order.
	 */
	public function save_enrollment( $post, $enrollment_arr, $enrollment_action, $order_id = null ) {

		$post_id = $post->ID;

		// Prepare enrollment data function call.
		$data = $this->prepare_enrollment_data( $post_id, $enrollment_arr );

		// Split returned arrays into 2 variables for old and current data.
		$old_data        = $data['old_data_array'];
		$enrollment_data = $data['enrollment_arr'];

		// Check if the enrollment main data is empty.
		if ( $this->is_enrollment_data_empty( $enrollment_data ) ) {
			return;
		}

		// Update post meta data for the Enrollment.
		$this->update_enrollment_meta_data( $post_id, $enrollment_data );

		/*
		 * Check if old post_meta tags are different from the new ones to
		 * change Enrollment Request name in Enrollment Manager requests list.
		 */

		if (
			$old_data['enrollment_course_id'] !== $enrollment_data['enrollment_course_id']
			|| $old_data['enrollment_email'] !== $enrollment_data['enrollment_email']
			|| $old_data['enrollment_mode'] !== $enrollment_data['enrollment_mode']
		) {
			$this->update_post( $post_id );
		}

		$non_valid_statuses = array( 'enrollment-success', 'enrollment-pending', 'enrollment-error' );

		// Only update the post status if it has no custom status yet.
		if ( ! in_array( $post->post_status, $non_valid_statuses, true ) ) {
			$this->update_post( $post_id, 'enrollment-pending' );
		}

		$api                     = new Openedx_Woocommerce_Plugin_Api_Calls();
		$enrollment_api_response = $api->enrollment_send_request( $enrollment_data, $enrollment_action );
		$this->log_manager->create_change_log( $post_id, $old_data, $enrollment_data, $enrollment_action, $enrollment_api_response );

		if ( null !== $order_id ) {
			$plugin_class = new Openedx_Woocommerce_Plugin();
			$admin_class  = new Openedx_Woocommerce_Plugin_Admin( $plugin_class->get_plugin_name(), $plugin_class->get_version() );
			$admin_class->show_enrollment_logs( $order_id, $enrollment_api_response );
		}
	}


	/**
	 * Check if important enrollment data is empty to stop operation
	 *
	 * @param array $enrollment_data An array containing the enrollment info.
	 */
	public function is_enrollment_data_empty( $enrollment_data ) {
		if (
			! $enrollment_data['enrollment_course_id']
			|| ! $enrollment_data['enrollment_email']
			|| ! $enrollment_data['enrollment_mode']
		) {
			return true;
		}
	}

	/**
	 * Prepare the array of information to use in the Enrollment process.
	 *
	 * @param string $post_id The Enrollment Request ID.
	 * @param array  $enrollment_arr An array containing the enrollment info.
	 */
	public function prepare_enrollment_data( $post_id, $enrollment_arr ) {

		// Sanitize enrollment arr.
		sanitize_text_field( $enrollment_arr['enrollment_course_id'] );
		sanitize_text_field( $enrollment_arr['enrollment_email'] );
		sanitize_text_field( $enrollment_arr['enrollment_mode'] );
		sanitize_text_field( $enrollment_arr['enrollment_request_type'] );
		sanitize_text_field( $enrollment_arr['enrollment_order_id'] );

		// Array of old post metadata.
		$old_data_array = array(
			'enrollment_course_id'    => get_post_meta( $post_id, 'course_id', true ),
			'enrollment_email'        => get_post_meta( $post_id, 'email', true ),
			'enrollment_mode'         => get_post_meta( $post_id, 'mode', true ),
			'enrollment_request_type' => get_post_meta( $post_id, 'enrollment_request_type', true ),
			'enrollment_order_id'     => get_post_meta( $post_id, 'order_id', true ),
		);

		// Return both arrays.
		return array(
			'enrollment_arr' => $enrollment_arr,
			'old_data_array' => $old_data_array,
		);
	}

	/**
	 * Update the Enrollment Request metadata with the current information.
	 *
	 * @param string $post_id The Enrollment Request ID.
	 * @param array  $enrollment_data An array containing the enrollment info.
	 */
	public function update_enrollment_meta_data( $post_id, $enrollment_data ) {
		// Update the $post metadata.
		update_post_meta( $post_id, 'course_id', $enrollment_data['enrollment_course_id'] );
		update_post_meta( $post_id, 'email', $enrollment_data['enrollment_email'] );
		update_post_meta( $post_id, 'mode', $enrollment_data['enrollment_mode'] );
		update_post_meta( $post_id, 'order_id', $enrollment_data['enrollment_order_id'] );
		update_post_meta( $post_id, 'enrollment_request_type', $enrollment_data['enrollment_request_type'] );

		if ( 'enroll' === $enrollment_data['enrollment_request_type'] ) {
			update_post_meta( $post_id, 'is_active', true );
		}
		if ( 'unenroll' === $enrollment_data['enrollment_request_type'] ) {
			update_post_meta( $post_id, 'is_active', false );
		}
	}

	/**
	 * Update post status
	 *
	 * @param int    $post_id The post ID.
	 * @param string $status The status of the request.
	 */
	public function update_post( $post_id, $status = null ) {

		$enrollment_course_id = get_post_meta( $post_id, 'course_id', true );
		$enrollment_email     = get_post_meta( $post_id, 'email', true );
		$enrollment_mode      = get_post_meta( $post_id, 'mode', true );

		$post_update = array(
			'ID'         => $post_id,
			'post_title' => $enrollment_course_id . ' | ' . $enrollment_email . ' | Mode: ' . $enrollment_mode,
		);

		if ( $status ) {
			$post_update['post_status'] = $status;
		}

		$this->wp_update_post( $post_update );
	}
}
