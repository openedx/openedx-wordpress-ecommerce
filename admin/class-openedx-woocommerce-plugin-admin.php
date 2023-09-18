<?php
/**
 * Openedx plugin admin file.
 * Admin file to manage the plugin.
 *
 * @category   Admin
 * @package    WordPress
 * @subpackage Openedx_Woocommerce_Plugin
 * @since      1.0.0
 */

namespace App\admin;

use App\model\Openedx_Woocommerce_Plugin_Enrollment;
use App\model\Openedx_Woocommerce_Plugin_Post_Type;
use App\admin\views\Openedx_Woocommerce_Plugin_Enrollment_Info_Form;
use App\utils;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://edunext.co/
 * @since      1.0.0
 *
 * @package    Openedx_Woocommerce_Plugin
 * @subpackage Openedx_Woocommerce_Plugin/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Openedx_Woocommerce_Plugin
 * @subpackage Openedx_Woocommerce_Plugin/admin
 * @author     eduNEXT <maria.magallanes@edunext.co>
 */
class Openedx_Woocommerce_Plugin_Admin {

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
	 * Create an instance of the Openedx_Woocommerce_Plugin_Enrollment class.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function create_enrollment_class() {
		$this->openedx_enrollment = new Openedx_Woocommerce_Plugin_Enrollment( $this );
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
		 * defined in Openedx_Woocommerce_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Openedx_Woocommerce_Plugin_Loader will then create the relationship
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
		 * defined in Openedx_Woocommerce_Plugin_Loader as
		 * all of the hooks are defined in that particular class.
		 *
		 * The Openedx_Woocommerce_Plugin_Loader will then create the relationship
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
		$this->openedx_enrollment_info_form = new Openedx_Woocommerce_Plugin_Enrollment_Info_Form( $post );
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
	 * Create a new instance of the Openedx_Woocommerce_Plugin_Post_Type class and register a new post type.
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
		return new Openedx_Woocommerce_Plugin_Post_Type( $post_type, $plural, $single, $description, $options );
	}

	/**
	 * Register course ID and mode fields for product
	 *
	 * @since    1.1.1
	 */
	public function add_custom_product_fields() {

		global $post;

		echo '<div class="options_group">';

		echo '<p class="form-field">' .
			esc_html__( 'Only use these fields if the product is an Open edX course.', 'woocommerce' )
			. '</p>';

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
	 * @param array $_product Product object.
	 * @param array $item Order item.
	 * @param int   $item_id Order item id.
	 *
	 * @return void
	 */
	public function add_admin_order_item_values( $_product, $item, $item_id = null ) {

		// Check if the product has a non-empty "_course_id" metadata.
		$_course_id = get_post_meta( $_product->get_id(), '_course_id', true );

		if ( ! empty( $_course_id ) ) {

			$order_id    = method_exists( $item, 'get_order_id' ) ? $item->get_order_id() : $item['order_id'];
			$input_value = get_post_meta( $order_id, 'enrollment_id' . $item_id, true );
			$order_url   = esc_url( admin_url( 'post.php?post=' . intval( $input_value ) . '&action=edit' ) );

			$html_output  = '<td>';
			$html_output .= '<input style="height:30px;" type="text" name="order_id_input' . esc_attr( $item_id ) . '" value="' . esc_attr( $input_value ) . '" pattern="\d*" />';
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

		foreach ( $items as $item_id => $item ) {
			if ( isset( $_POST[ 'order_id_input' . $item_id ] ) ) {
				$input_value = sanitize_text_field( wp_unslash( $_POST[ 'order_id_input' . $item_id ] ) );
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
		$course_id = isset( $_POST['_course_id'] ) ? sanitize_text_field( wp_unslash( $_POST['_course_id'] ) ) : '';
		$mode      = isset( $_POST['_mode'] ) ? sanitize_text_field( wp_unslash( $_POST['_mode'] ) ) : '';

		update_post_meta( $post_id, '_course_id', $course_id );
		update_post_meta( $post_id, '_mode', $mode );
	}

	public function process_order_data( $order_id, $new_status ) {

		$order         = wc_get_order( $order_id );
		$billing_email = $order->get_billing_email();
		$status        = $order->get_status();
		$enrollment_id = '';
		$courses       = [];

		if ( $status === 'processing' ) {

			$courses = $this->select_course_items( $order, $billing_email );

			if ( ! empty( $courses ) ) {
				wc_create_order_note( $order_id, 'Order items that are courses, obtained. ' );
				$enrollment_id = $this->items_enrollment_request( $courses, $order_id, $billing_email );
			}
		}
	}

	public function select_course_items( $order, $billing_email ) {

		$items         = $order->get_items();
		$courses       = [];

		foreach ( $items as $item_id => $item ) {
			$product_id = $item->get_product_id();
			$course_id  = get_post_meta( $product_id, '_course_id', true );

			if ( $course_id !== '' ) { 
				$courses[] = array( $item, $item_id );
			}
		}

		return $courses;
	}

	public function items_enrollment_request( $courses, $order_id, $billing_email ) {

		foreach ( $courses as $item_id => $item ) {
				
			$course_id = get_post_meta( $item[0]->get_product_id() , '_course_id', true );
			$course_mode = get_post_meta( $item[0]->get_product_id(), '_mode', true );
			$request_type = 'enroll';
			$action = "enrollment_process";
			
			$enrollment_arr = array(
				'enrollment_course_id' => $course_id,
				'enrollment_email' => $billing_email,
				'enrollment_mode' => $course_mode,
				'enrollment_request_type' => $request_type,
				'enrollment_order_id' => $order_id
			);

			$enrollment_id = $this->openedx_enrollment->insert_new( $enrollment_arr, $action, $order_id );
			update_post_meta( $order_id, 'enrollment_id' . $item[1], $enrollment_id->ID );
			wc_create_order_note( $order_id, 'Enrollment Request ID: ' . $enrollment_id->ID . " created. Click <a href='" . admin_url( 'post.php?post=' . intval( $enrollment_id->ID ) . '&action=edit' ) . "'>here</a> to see the Enrollment Request." );
		}
	}

	public function show_enrollment_logs( $order_id, $enrollment_api_response ) {
		$response = $this->check_api_response( $enrollment_api_response );
		wc_create_order_note( $order_id, $response );
	}

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
}
