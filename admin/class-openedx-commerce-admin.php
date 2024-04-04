<?php
/**
 * Openedx plugin admin file.
 * Admin file to manage the plugin.
 *
 * @category   Admin
 * @package    WordPress
 * @subpackage Openedx_Commerce
 * @since      1.0.0
 */

namespace OpenedX_Commerce\admin;

use OpenedX_Commerce\model\Openedx_Commerce_Enrollment;
use OpenedX_Commerce\model\Openedx_Commerce_Post_Type;
use OpenedX_Commerce\admin\views\Openedx_Commerce_Enrollment_Info_Form;
use OpenedX_Commerce\utils;


/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://edunext.co/
 * @since      1.0.0
 *
 * @package    Openedx_Commerce
 * @subpackage Openedx_Commerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Openedx_Commerce
 * @subpackage Openedx_Commerce/admin
 * @author     eduNEXT <maria.magallanes@edunext.co>
 */
class Openedx_Commerce_Admin {

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
	 * The enrollment instance.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $openedx_enrollment    The current instance of enrollment request.
	 */
	public $openedx_enrollment;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_name        The name of this plugin.
	 * @param string $version        The version of this plugin.
	 * @param string $test        Flag variable to know if it is a test.
	 */
	public function __construct( $plugin_name, $version, $test = null ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		if ( ! $test ) {
			$this->create_enrollment_class();
		}
	}

	/**
	 * Create an instance of the Openedx_Commerce_Enrollment class.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function create_enrollment_class() {
		$this->openedx_enrollment = new Openedx_Commerce_Enrollment( $this );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Openedx_Commerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Openedx_Commerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Openedx_Commerce_Loader as
		 * all of the hooks are defined in that particular class.
		 *
		 * The Openedx_Commerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
	}

	/**
	 * Register Enrollment Request custom post type
	 *
	 * @since    1.0.0
	 */
	public function register_enrollment_custom_post_type() {
		$this->openedx_enrollment->register_enrollment_custom_post_type();
	}

	/**
	 * Render Enrollment Request info form
	 *
	 * @param WP_Post $post Current post object.
	 * @since    1.0.0
	 */
	public function render_enrollment_info_form( $post ) {
		$this->openedx_enrollment_info_form = new Openedx_Commerce_Enrollment_Info_Form( $post );
	}

	/**
	 * Wrapper function to register a new post type
	 *
	 * @param  string $post_type   Post type name.
	 * @param  string $plural      Post type item plural name.
	 * @param  string $single      Post type item single name.
	 * @param  string $description Description of post type.
	 * @param  array  $options     Additional options for the post type.
	 *
	 * @return object              Post type class object
	 */
	public function register_post_type( $post_type = '', $plural = '', $single = '', $description = '', array $options ) {

		if ( ! $post_type || ! $plural || ! $single ) {
			return;
		}

		$post_type = $this->create_post_type( $post_type, $plural, $single, $description, $options );

		return $post_type;
	}

	/**
	 * Create a new instance of the Openedx_Commerce_Post_Type class and register a new post type.
	 *
	 * @param  string $post_type   Post type name.
	 * @param  string $plural      Post type item plural name.
	 * @param  string $single      Post type item single name.
	 * @param  string $description Description of the post type.
	 * @param  array  $options     Additional options for the post type.
	 * @return object              Post type class object.
	 */
	public function create_post_type(
		$post_type = '',
		$plural = '',
		$single = '',
		$description = '',
		array $options
	) {
		return new Openedx_Commerce_Post_Type( $post_type, $plural, $single, $description, $options );
	}

	/**
	 * Add Open edX Course product type option in product settings.
	 *
	 * @param array $type_options Array of product type options.
	 * @return array $type_options Array of product type options.
	 */
	public function add_openedx_course_product_type( $type_options ) {

		global $post;
		$checked = '';

		if ( ! empty( get_post_meta( $post->ID, 'is_openedx_course', true ) ) ) {
			$checked = get_post_meta( $post->ID, 'is_openedx_course', true );
		} else {
			$checked = 'no';
		}

		$type_options['wild_card'] = array(
			'id'            => 'is_openedx_course',
			'wrapper_class' => 'show_if_simple',
			'label'         => __( 'Open edX Course', 'woocommerce' ),
			'description'   => __( 'Check this box if the product is an Open edX Course', 'woocommerce' ),
			'default'       => $checked,
		);
		return $type_options;
	}

	/**
	 * Save the Open edX course product type option value into a post meta field.
	 *
	 * @param int $post_id Product post id.
	 * @return void
	 */
	public function save_openedx_option( $post_id ) {
		if ( ! isset( $_POST['openedx_commerce_custom_product_nonce'] ) ||
			! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['openedx_commerce_custom_product_nonce'] ) ), 'openedx_commerce_custom_product_nonce' )
		) {
			return;
		}
		$openedx_course = isset( $_POST['is_openedx_course'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, 'is_openedx_course', $openedx_course );
	}

	/**
	 * Register course ID and mode fields for product
	 *
	 * @since    1.1.1
	 */
	public function add_custom_product_fields() {

		global $post;

		$nonce = wp_create_nonce( 'openedx_commerce_custom_product_nonce' );

		echo '<div class="custom_options_group">';

		echo '<input type="hidden" name="openedx_commerce_custom_product_nonce" value="' . esc_attr( $nonce ) . '">';

		woocommerce_wp_text_input(
			array(
				'id'          => '_course_id',
				'label'       => __( 'Open edX Course ID', 'woocommerce' ),
				'placeholder' => '',
				'desc_tip'    => 'true',
				'description' => __(
					'Ex: course-v1:edX+DemoX+Demo_Course.
					<br><br> You can find the Open edX Course ID
					in the URL of your course in your LMS.',
					'woocommerce',
				),
			)
		);

		woocommerce_wp_select(
			array(
				'id'          => '_mode',
				'label'       => __( 'Open edX Course Mode', 'woocommerce' ),
				'desc_tip'    => 'true',
				'description' => __(
					'Select the mode for your course. 
					Make sure to set a mode that your course has.',
					'woocommerce',
				),
				'options'     => utils\get_enrollment_options(),
			)
		);

		echo '</div>';
	}

	/**
	 * Create a custom column in order items table inside an order
	 *
	 * @return void
	 */
	public function add_custom_column_order_items() {
		echo '<th>' .
			esc_html__( 'Related Enrollment Request', 'woocommerce' ) .
			'</th>';
	}

	/**
	 * Create a custom input in the new column in order items table
	 * to store the enrollment id and a link to the enrollment request
	 *
	 * @param array $product Product object.
	 * @param array $item Order item.
	 * @param int   $item_id Order item id.
	 *
	 * @return void
	 */
	public function add_admin_order_item_values( $product, $item, $item_id = null ) {

		// Check if the product has a non-empty "_course_id" metadata.
		$course_id = '';
		$nonce     = wp_create_nonce( 'openedx_commerce_order_item_nonce' );

		if ( $product ) {
			$course_id    = get_post_meta( $product->get_id(), '_course_id', true );
			$course_check = get_post_meta( $product->get_id(), 'is_openedx_course', true );
		}

		if ( ! empty( $course_id ) && 'yes' === $course_check ) {

			$order_id    = method_exists( $item, 'get_order_id' ) ? $item->get_order_id() : $item['order_id'];
			$input_value = get_post_meta( $order_id, 'enrollment_id' . $item_id, true );
			$order_url   = esc_url( admin_url( 'post.php?post=' . intval( $input_value ) . '&action=edit' ) );

			$html_output  = '<td>';
			$html_output .= '<input type="hidden" name="openedx_commerce_order_item_nonce"  value="' . esc_attr( $nonce ) . '">';
			$html_output .= '<input style="height:30px;" type="text" name="openedx_order_id_input' . esc_attr( $item_id ) . '" value="' . esc_attr( $input_value ) . '" pattern="\d*" />';
			$html_output .= '<a href="' . $order_url . '" class="button" style="margin-left: 5px; vertical-align: bottom;' . ( $input_value ? '' : 'pointer-events: none; opacity: 0.6;' ) . '">View Request</a>';
			$html_output .= '</td>';

			echo wp_kses(
				$html_output,
				array(
					'a'     => array(
						'href'  => array(),
						'class' => array(),
						'style' => array(),
					),
					'input' => array(
						'type'    => array(),
						'name'    => array(),
						'value'   => array(),
						'pattern' => array(),
						'style'   => array(),
					),
					'td'    => array(),
				)
			);
		} else {

			$html_output  = '<td>';
			$html_output .= esc_html__( 'Product is not an Open edX Course', 'woocommerce' );
			$html_output .= '</td>';

			echo wp_kses(
				$html_output,
				array(
					'td' => array(),
				)
			);
		}
	}

	/**
	 * Save the enrollment id in the order meta data
	 *
	 * @param int $order_id Order id.
	 *
	 * @return void
	 */
	public function save_order_meta_data( $order_id ) {

		$items = wc_get_order( $order_id )->get_items();

		if ( ! isset( $_POST['openedx_commerce_order_item_nonce'] ) ||
			! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['openedx_commerce_order_item_nonce'] ) ), 'openedx_commerce_order_item_nonce' )
		) {
			return;
		}

		foreach ( $items as $item_id => $item ) {
			if ( isset( $_POST[ 'openedx_order_id_input' . $item_id ] ) ) {
				$input_value = sanitize_text_field( wp_unslash( $_POST[ 'openedx_order_id_input' . $item_id ] ) );
				update_post_meta( $order_id, 'enrollment_id' . $item_id, $input_value );
			}
		}
	}

	/**
	 * Save course ID and mode fields for product
	 *
	 * @param int $post_id Post id.
	 *
	 * @since    1.1.1
	 */
	public function save_custom_product_fields( $post_id ) {
		if ( ! isset( $_POST['openedx_commerce_custom_product_nonce'] ) ||
			! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['openedx_commerce_custom_product_nonce'] ) ), 'openedx_commerce_custom_product_nonce' )
		) {
			return;
		}
		$course_id = isset( $_POST['_course_id'] ) ? sanitize_text_field( wp_unslash( $_POST['_course_id'] ) ) : '';
		$mode      = isset( $_POST['_mode'] ) ? sanitize_text_field( wp_unslash( $_POST['_mode'] ) ) : '';

		update_post_meta( $post_id, '_course_id', $course_id );
		update_post_meta( $post_id, '_mode', $mode );
	}

	/**
	 * This function is called when an order payment is completed.
	 *
	 * @param int $order_id Order id.
	 *
	 * @return void
	 */
	public function process_order_data( $order_id ) {

		$order         = wc_get_order( $order_id );
		$status        = $order->get_status();
		$enrollment_id = '';
		$courses       = array();

		if ( 'processing' === $status ) {

			$billing_email = $order->get_billing_email();
			$order_items   = $order->get_items();
			$courses       = $this->select_course_items( $order_items );

			if ( ! empty( $courses ) ) {
				wc_create_order_note( $order_id, 'Order items that are courses, obtained. ' );
				$enrollment_id = $this->items_enrollment_request( $courses, $order_id, $billing_email, 'enroll' );
			}
		}
	}

	/**
	 * Select the items that are courses in the order.
	 *
	 * @param array $items Order items array.
	 * @param bool  $is_refunded Flag variable to know if the order is refunded.
	 *
	 * @return array $courses Array of courses.
	 */
	public function select_course_items( $items, $is_refunded = false ) {

		$courses = array();

		foreach ( $items as $item_id => $item ) {

			$product_id   = $item->get_product_id();
			$course_id    = get_post_meta( $product_id, '_course_id', true );
			$course_check = get_post_meta( $product_id, 'is_openedx_course', true );

			if ( '' !== $course_id && 'yes' === $course_check ) {

				if ( $is_refunded ) {
					$courses[] = array(
						'course_item'    => $item,
						'course_item_id' => $item->get_meta( '_refunded_item_id' ),
					);
				} else {
					$courses[] = array(
						'course_item'    => $item,
						'course_item_id' => $item_id,
					);
				}
			}
		}

		return $courses;
	}

	/**
	 * This function processes the enrollment request for each course in the order.
	 *
	 * @param array  $courses Array of courses.
	 * @param int    $order_id Order id.
	 * @param string $billing_email Billing email.
	 * @param string $request_type Request type.
	 *
	 * @return void
	 */
	public function items_enrollment_request( $courses, $order_id, $billing_email, $request_type ) {

		foreach ( $courses as $item_id => $item ) {

			$course_id   = get_post_meta( $item['course_item']->get_product_id(), '_course_id', true );
			$course_mode = get_post_meta( $item['course_item']->get_product_id(), '_mode', true );
			$action      = 'enrollment_process';

			$enrollment_arr = array(
				'openedx_enrollment_course_id'    => $course_id,
				'openedx_enrollment_email'        => $billing_email,
				'openedx_enrollment_mode'         => $course_mode,
				'openedx_enrollment_request_type' => $request_type,
				'openedx_enrollment_order_id'     => $order_id,
			);

			$enrollment_id = $this->openedx_enrollment->insert_new( $enrollment_arr, $action, $order_id );
			update_post_meta( $order_id, 'enrollment_id' . $item['course_item_id'], $enrollment_id->ID );

			if ( 'enroll' === $request_type ) {
				wc_create_order_note( $order_id, 'Enrollment Request ID: ' . $enrollment_id->ID . " Click <a href='" . admin_url( 'post.php?post=' . intval( $enrollment_id->ID ) . '&action=edit' ) . "'>here</a> for details." );
			} else {
				wc_create_order_note( $order_id, 'Unenroll Request ID: ' . $enrollment_id->ID . " Click <a href='" . admin_url( 'post.php?post=' . intval( $enrollment_id->ID ) . '&action=edit' ) . "'>here</a> for details." );
			}
		}
	}

	/**
	 * Shows the API logs in the order notes.
	 *
	 * @param int   $order_id Order id.
	 * @param array $enrollment_api_response The API response.
	 *
	 * @return void
	 */
	public function show_enrollment_logs( $order_id, $enrollment_api_response ) {
		$response = $this->check_api_response( $enrollment_api_response );
		wc_create_order_note( $order_id, $response );
	}

	/**
	 * Check the API response from the Open edX API to show a simple message about the response.
	 * The message will be displayed in order notes.
	 *
	 * @param array $response The API response.
	 *
	 * @return string $response The API response.
	 */
	public function check_api_response( $response ) {

		switch ( $response[0] ) {

			case 'error':
				return 'Open edX platform response: ' . $response[1];

			case 'success':
				return 'The Open edX platform processed the request.';

			default:
				return 'API did not provide a response';
		}
	}

	/**
	 * Process unenrollment requests for courses in a refunded order.
	 * Loop through the refunded items, select courses, and send unenrollment requests to Open edX API.
	 *
	 * @param int $order_id Order id.
	 *
	 * @return void
	 */
	public function unenroll_course_refund( $order_id ) {

		$order         = wc_get_order( $order_id );
		$billing_email = $order->get_billing_email();

		if ( ! $order ) {
			return;
		}

		$refunds = $order->get_refunds();

		foreach ( $refunds as $refund ) {
			$items   = $refund->get_items();
			$courses = $this->select_course_items( $items, true );
			if ( ! empty( $courses ) ) {
				wc_create_order_note( $order_id, 'Order items that are courses and refunded, obtained. ' );
				$enrollment_id = $this->items_enrollment_request( $courses, $order_id, $billing_email, 'unenroll' );
			}
		}
	}
}
