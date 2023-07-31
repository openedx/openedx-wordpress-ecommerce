<?php

namespace App\model;
use App\model\Openedx_Woocommerce_Plugin_Log;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

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
     * Constructor function.
     *
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function __construct( $parent ) {
        $this->parent = $parent;

        // Register all the model related hooks
        $this->register_hook_callbacks();
    }

    protected function register_hook_callbacks() {
        /**
         * If you think all model related add_actions & filters should be in
         * the model class only, then this this the place where you can place
         * them.
         *
         * You can remove this method if you are not going to use it.
         */

        // Add types of status to the enrollment request custom-post-type
        add_action( 'init', array( $this, 'register_status' ), 10, 3 );

        // Add the enrollment logic to the save post hook
        add_action( 'save_post', array( $this, 'save_action' ), 10, 3 );
    }

    public function register_enrollment_custom_post_type(){
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
                'all_items' => 'Enrollments Manager',
                'add_new_item' => 'Add New Enrollment Request',
                'edit_item' => 'Edit Enrollment Request',
            ),
        );
        
         // Register post-type using wrapper custom-post-type function
        $this->parent->register_post_type('openedx_enrollment', ' ', ' ', '', $enrollment_cpt_options);
    }

    /**
     * Unregister the save hook to prevent an infinite cycle of hook recursion
     * 
     * @return void 
     */
    public function unregisterSaveHook()
    {
        remove_action('save_post', array($this, 'save_action'), 10, 3);
    }

    /**
     * Register the save hook to prevent an infinite cycle of hook recursion
     * 
     * @return void
     */
    public function registerSaveHook()
    {
        add_action('save_post', array($this, 'save_action'), 10, 3);
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
                'label'                     => __( 'Success', 'wp-edunext-marketing-site' ),
                'public'                    => false,
                'internal'                  => true,
                'private'                   => true,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop( 'Success <span class="count">(%s)</span>', 'Success <span class="count">(%s)</span>', 'wp-edunext-marketing-site' ),
            )
        );
        register_post_status(
            'enrollment-pending',
            array(
                'label'                     => __( 'Pending', 'wp-edunext-marketing-site' ),
                'public'                    => false,
                'internal'                  => true,
                'private'                   => true,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>', 'wp-edunext-marketing-site' ),
            )
        );
        register_post_status(
            'enrollment-error',
            array(
                'label'                     => __( 'Error', 'wp-edunext-marketing-site' ),
                'public'                    => false,
                'internal'                  => true,
                'private'                   => true,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop( 'Error <span class="count">(%s)</span>', 'Error <span class="count">(%s)</span>', 'wp-edunext-marketing-site' ),
            )
        );
    }

    /**
     * Wrapper for the WP function that prevents an infinite cycle of hook recursion
     *
     * @param array $post The post info in an array.
     */
    public function wp_update_post( $post ) {
        $this->unregisterSaveHook();

        wp_update_post( $post );

        $this->registerSaveHook();
    }

    /**
     * Save post metadata when a post is saved.
     *
     * @param int  $post_id The post ID.
     * @param post $post The post object.
     * @param bool $update Whether this is an existing post being updated or not.
     */
    public function save_action( $post_id, $post, $update ) {

        if ( $this->post_type !== $post->post_type ) {
            return;
        }

        $enrollment_arr = array(
        'enrollment_course_id' => sanitize_text_field($_POST['enrollment_course_id'] ?? ''),
        'enrollment_email' => sanitize_text_field($_POST['enrollment_email'] ?? ''),
        'enrollment_mode' => sanitize_text_field($_POST['enrollment_mode'] ?? ''),
        'enrollment_request_type' => sanitize_text_field(
            $_POST['enrollment_request_type'] ?? ''
        ),
        'enrollment_order_id' => sanitize_text_field($_POST['enrollment_order_id'] ?? ''),
        );
    
        $enrollment_action = sanitize_text_field($_POST['enrollment_action'] ?? '');

        $this->save_enrollment( $post, $enrollment_arr, $enrollment_action );
    }

    /**
     * Creates a new post in the database and runs it through the save.
     *
     * @param array  $enrollment_arr An array containing the enrollment info.
     * @param string $enrollment_action The API action to perform once the wp process is done.
     *
     * @return object $post The post object.
     */
    public function insert_new( $enrollment_arr, $enrollment_action = '' ) {
        $this->unregister_save_hook();

        $new_enrollment = array(
            'post_content' => 'Created automatically by woocommerce to fullfill an order.',
            'post_type'    => 'openedx_enrollment',
        );
        $post_id = wp_insert_post( $new_enrollment );
        $post    = get_post( $post_id );

        $this->save_enrollment( $post, $enrollment_arr, $enrollment_action );
        return $post;
    }

    /**
     * Save openedx request based on the incomming args.
     *
     * @param post   $post The post object.
     * @param array  $enrollment_arr An array containing the enrollment info.
     * @param string $enrollment_action The API action to perform once the wp process is done.
     */
    public function save_enrollment( $post, $enrollment_arr, $enrollment_action ) {

        $post_id = $post->ID;
        $old_post = $post;

        $enrollment_course_id    = $enrollment_arr['enrollment_course_id'];
        $enrollment_email        = $enrollment_arr['enrollment_email'];
        $enrollment_mode         = $enrollment_arr['enrollment_mode'];
        $enrollment_request_type = $enrollment_arr['enrollment_request_type'];
        $enrollment_order_id     = $enrollment_arr['enrollment_order_id'];

        // Get the old post metadata.
        $old_course_id           = get_post_meta($post_id, 'course_id', true);
        $old_email               = get_post_meta($post_id, 'email', true);
        $old_mode                = get_post_meta($post_id, 'mode', true);
        $old_request_type        = get_post_meta($post_id, 'is_active', true);
        $old_order_id            = get_post_meta($post_id, 'order_id', true);

        // Array of old data
        $old_data_array = array(
            'enrollment_course_id' => $old_course_id,
            'enrollment_email' => $old_email,
            'enrollment_mode' => $old_mode,
            'enrollment_request_type' => $old_request_type,
            'enrollment_order_id' => $old_order_id
        );

        // We need to have all 3 required params to continue.
        $enrollment_user_reference = $enrollment_email;
        if ( ! $enrollment_course_id || ! $enrollment_user_reference || ! $enrollment_mode ) {
            return;
        }

        // Update the $post metadata.
        update_post_meta( $post_id, 'course_id', $enrollment_course_id );
        update_post_meta( $post_id, 'email', $enrollment_email );
        update_post_meta( $post_id, 'mode', $enrollment_mode );
        update_post_meta( $post_id, 'order_id', $enrollment_order_id );

        if ( $enrollment_request_type === 'enroll' ) {
            update_post_meta( $post_id, 'is_active', true );
        }
        if ( $enrollment_request_type === 'unenroll' ) {
            update_post_meta( $post_id, 'is_active', false );
        }

        // Check if old post_meta tags are different from the new ones to change post name.

        if ($old_course_id !== $enrollment_course_id 
            || $old_email !== $enrollment_email         
            || $old_mode !== $enrollment_mode) {
            $this->updatePost($post_id);
        }

        // Only update the post status if it has no custom status yet.
        if ($post->post_status !== 'enrollment-success' 
            && $post->post_status !== 'enrollment-pending' 
            && $post->post_status !== 'enrollment-error') {
            $this->updatePost($post_id, 'enrollment-pending');
        }


        $this->createChangeLog($post_id, $old_data_array, $enrollment_arr, $enrollment_action);
    }


    /**
     * Creates a new log entry in the database.
     * 
     * @param int $post_id The post ID.
     * @param array $old_data_array The old data array.
     * @param array $enrollment_arr The new data array.
     * @param string $enrollment_action The API action to perform.
     * 
     * @return void
     */
    public function createChangeLog( $post_id, $old_data_array, $enrollment_arr, $enrollment_action ) {

        global $wpdb;
        $tabla_logs = $wpdb->prefix . 'enrollment_logs_req_table';
        $new_post = get_post($post_id);
    
        $fecha_registro = current_time('mysql', true);
        $usuario_id = get_current_user_id(); 
        $usuario_nombre = get_user_by('id', $usuario_id)->user_login; 
    
        // Cambiar el valor de "action_name" para un nuevo post o cuando se envía a la papelera
        if (empty($old_data_array['enrollment_course_id'])) {
            $enrollment_action = 'Object Created';
        } 
    
        // Cambiar el valor de "object_before" si está vacío
        if (empty($old_data_array['enrollment_course_id'])) {
            $old_data_array = '--';
        }
    
        $log_data = array(
            'post_id' => $post_id,
            'fecha_registro' => $fecha_registro,
            'user' => $usuario_nombre,
            'action_name' => $enrollment_action,
            'object_before' => json_encode($old_data_array),
            'object_after' => json_encode($enrollment_arr)
        );
    
        $wpdb->insert($tabla_logs, $log_data);
    
        if ($wpdb->last_error) {
            error_log('There was an error generating a register: ' . $wpdb->last_error);
        }
    }

    /**
     * Update post status
     *
     * @param string $status The status of the request.
     * @param int    $post_id The post ID.
     */
    public function updatePost( $post_id, $status = null )
    {

        $enrollment_course_id = get_post_meta($post_id, 'course_id', true);
        $enrollment_email  = get_post_meta($post_id, 'email', true);
        $enrollment_mode      = get_post_meta($post_id, 'mode', true);

        $post_update = array(
            'ID'         => $post_id,
            'post_title' 
                => $enrollment_course_id .' | '. $enrollment_email .' | Mode: '.$enrollment_mode,
        );
        
        if ($status) {
            $post_update['post_status'] = $status;
        }

        $this->wp_update_post($post_update);
    }
}
