<?php

namespace App\model;

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
        );
        
        // Register post-type using wrapper custom-post-type function
        $this->parent->register_post_type( 'openedx_enrollment', 'Open edX Enrollment Requests', 'Open edX Enrollment Request', '', $enrollment_cpt_options );
    }

    public function unregister_save_hook() {
        remove_action( 'save_post', array( $this, 'save_action' ), 10, 3 );
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
        $this->unregister_save_hook();

        wp_update_post( $post );

        $this->register_save_hook();
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
            'enrollment_course_id'    => sanitize_text_field( $_POST['enrollment_course_id'] ),
            'enrollment_email'        => sanitize_text_field( $_POST['enrollment_email'] ),
            'enrollment_username'     => sanitize_text_field( $_POST['enrollment_username'] ),
            'enrollment_mode'         => sanitize_text_field( $_POST['enrollment_mode'] ),
            'enrollment_request_type' => sanitize_text_field( $_POST['enrollment_request_type'] ),
            'enrollment_order_id'     => sanitize_text_field( $_POST['enrollment_order_id'] ),
        );

        $enrollment_action = sanitize_text_field( $_POST['enrollment_action'] );

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

        $enrollment_course_id    = $enrollment_arr['enrollment_course_id'];
        $enrollment_email        = $enrollment_arr['enrollment_email'];
        $enrollment_username     = $enrollment_arr['enrollment_username'];
        $enrollment_mode         = $enrollment_arr['enrollment_mode'];
        $enrollment_request_type = $enrollment_arr['enrollment_request_type'];
        $enrollment_order_id     = $enrollment_arr['enrollment_order_id'];

        // We need to have all 3 required params to continue.
        $enrollment_user_reference = $enrollment_email || $enrollment_username;
        if ( ! $enrollment_course_id || ! $enrollment_user_reference || ! $enrollment_mode ) {
            return;
        }

        // Update the $post metadata.
        update_post_meta( $post_id, 'course_id', $enrollment_course_id );
        update_post_meta( $post_id, 'email', $enrollment_email );
        update_post_meta( $post_id, 'username', $enrollment_username );
        update_post_meta( $post_id, 'mode', $enrollment_mode );
        update_post_meta( $post_id, 'order_id', $enrollment_order_id );

        if ( $enrollment_request_type === 'enroll' ) {
            update_post_meta( $post_id, 'is_active', true );
        }
        if ( $enrollment_request_type === 'unenroll' ) {
            update_post_meta( $post_id, 'is_active', false );
        }

        // Only update the post status if it has no custom status yet.
        if ( $post->post_status !== 'enrollment-success' && $post->post_status !== 'enrollment-pending' && $post->post_status !== 'enrollment-error' ) {
            $this->update_post_status( 'enrollment-pending', $post_id );
        }
    }

    /**
     * Save post metadata when a post is saved.
     *
     * @param int  $post_id The post ID.
     * @param bool $force Does this order need processing by force?.
     */
    public function process_request( $post_id, $force, $do_pre_enroll = true ) {

        $user_args = $this->prepare_args( $post_id, 'user' );
        $user      = WP_EoxCoreApi()->get_user_info( $user_args );

        // If the user doesn't exist create pre-enrollment with the email provided.
        if ( is_wp_error( $user ) && $do_pre_enroll ) {
            if ( ! empty( $user_args['email'] ) ) {
                $pre_enrollment_args = $this->prepare_args( $post_id, 'pre-enrollment' );
                $this->create_pre_enrollment( $post_id, $pre_enrollment_args );
                return;
            } else {
                // TODO Polish error message display.
                update_post_meta( $post_id, 'errors', 'A valid username or email is needed.' );
                $this->update_post_status( 'enrollment-error', $post_id );
                return;
            }
        }
        $enrollment_args          = $this->prepare_args( $post_id, 'enrollment' );
        $enrollment_args['force'] = $force;

        // When we reach this line we already have the user as a response from the API.
        // Better use that username in case anything from before does not match.
        $enrollment_args['username'] = $user->username;

        $enrollment = WP_EoxCoreApi()->get_enrollment( $enrollment_args );

        // If the enrollment already exists update it.
        if ( is_wp_error( $enrollment ) ) {
            $this->create_enrollment( $post_id, $enrollment_args );
        } else {
            $this->update_enrollment( $post_id, $enrollment_args );
        }
    }

    /**
     * Prepare args to be passed to the api calls
     *
     * @param int    $post_id  The post ID.
     * @param string $type The args type to be prepared (user, enrollment, ..).
     *
     * @return array args args ready to be pass
     */
    public function prepare_args( $post_id, $type ) {

        $args      = array();
        $user_args = array_filter(
            array(
                'email'    => get_post_meta( $post_id, 'email', true ),
                'username' => get_post_meta( $post_id, 'username', true ),
            )
        );

        $enrollment_args = array(
            'course_id' => get_post_meta( $post_id, 'course_id', true ),
        );

        $enrollment_opts_args = array(
            'mode'      => get_post_meta( $post_id, 'mode', true ),
            'is_active' => ( get_post_meta( $post_id, 'is_active', true ) ? true : false ),
        );

        switch ( $type ) {
            case 'user':
                return $user_args;
            case 'enrollment':
                // By the API design enrollment operations work on usernames.
                unset( $user_args['email'] );
                return array_merge( $user_args, $enrollment_args, $enrollment_opts_args );
            case 'pre-enrollment' or 'basic enrollment':
                return array_merge( $user_args, $enrollment_args );
        }

        return $args;
    }

    /**
     * Update post metadata when a post is synced.
     *
     * @param int $post_id The post ID.
     */
    public function sync_request( $post_id ) {

        $args     = $this->prepare_args( $post_id, 'basic enrollment' );
        $response = WP_EoxCoreApi()->get_enrollment( $args );

        if ( is_wp_error( $response ) ) {
            update_post_meta( $post_id, 'errors', $response->get_error_message() );

            // Update Status.
            $this->update_post_status( 'enrollment-error', $post_id );

        } else {
            delete_post_meta( $post_id, 'errors' );

            // Only this fields can be updated.
            update_post_meta( $post_id, 'mode', $response->mode );
            update_post_meta( $post_id, 'is_active', $response->is_active );

            // This fields should be updated if empty.
            if ( ! get_post_meta( $post_id, 'username', true ) ) {
                update_post_meta( $post_id, 'username', $response->username );
            }
            if ( ! get_post_meta( $post_id, 'email', true ) ) {
                $user_args = $this->prepare_args( $post_id, 'user' );
                $user      = WP_EoxCoreApi()->get_user_info( $user_args );
                update_post_meta( $post_id, 'email', $user->email );
            }

            // Update Status.
            $this->update_post_status( 'enrollment-success', $post_id );
        }
    }


    /**
     * Create enrollment.
     *
     * @param int   $post_id The post ID.
     * @param array $args The request parameters to be sent to the api.
     */
    public function create_enrollment( $post_id, $args ) {

        $response = WP_EoxCoreApi()->create_enrollment( $args );

        if ( is_wp_error( $response ) ) {
            update_post_meta( $post_id, 'errors', $response->get_error_message() );
            $status = 'enrollment-error';
        } else {
            delete_post_meta( $post_id, 'errors' );
            // This field should be updated if empty.
            if ( ! get_post_meta( $post_id, 'username', true ) ) {
                update_post_meta( $post_id, 'username', $response->username );
            }
            $status = 'enrollment-success';
        }

        $this->update_post_status( $status, $post_id );
    }

    /**
     * Create pre-enrollment.
     *
     * @param int   $post_id The post ID.
     * @param array $args The request parameters to be sent to the api.
     */
    public function create_pre_enrollment( $post_id, $args ) {
        $response = WP_EoxCoreApi()->create_pre_enrollment( $args );

        if ( is_wp_error( $response ) ) {
            update_post_meta( $post_id, 'errors', $response->get_error_message() );
            $status = 'enrollment-error';
        } else {
            update_post_meta( $post_id, 'errors', 'The provided user does not exist. A pre-enrollment for ' . $response->email . ' was created instead. ' );
            $status = 'enrollment-success';
        }
        $this->update_post_status( $status, $post_id );
    }

    /**
     * Update post status
     *
     * @param string $status The status of the request.
     * @param int    $post_id The post ID.
     */
    public function update_post_status( $status, $post_id ) {

        $enrollment_course_id = get_post_meta( $post_id, 'course_id', true );
        $enrollment_username  = get_post_meta( $post_id, 'username', true );
        $enrollment_mode      = get_post_meta( $post_id, 'mode', true );

        $post_update = array(
            'ID'          => $post_id,
            'post_status' => $status,
            'post_title'  => $enrollment_course_id . ' | ' . $enrollment_username . ' | Mode: ' . $enrollment_mode,
        );

        $this->wp_update_post( $post_update );
    }


    /**
     * Update enrollment.
     *
     * @param int   $post_id The post ID.
     * @param array $args The request parameters to be sent to the api.
     */
    public function update_enrollment( $post_id, $args ) {
        $response = WP_EoxCoreApi()->update_enrollment( $args );
        if ( is_wp_error( $response ) ) {
            update_post_meta( $post_id, 'errors', $response->get_error_message() );
            $this->update_post_status( 'enrollment-error', $post_id );
        } else {
            delete_post_meta( $post_id, 'errors' );
            update_post_meta( $post_id, 'mode', $response->mode );
            update_post_meta( $post_id, 'is_active', $response->is_active );
            $this->update_post_status( 'enrollment-success', $post_id );
        }
    }

    /**
     * Filters the list of actions available on the list view below each object
     *
     * @return actions
     */
    public function remove_table_row_actions( $actions ) {

        unset( $actions['edit'] );
        unset( $actions['trash'] );
        unset( $actions['view'] );
        unset( $actions['inline hide-if-no-js'] );

        return $actions;
    }

    /**
     * Adds the cpt columns to the list view
     *
     * @return array $column
     */
    public function add_columns_to_list_view( $column ) {
        $column['enrollment_status']   = 'Status';
        $column['enrollment_type']     = 'Type';
        $column['date']         = 'Date created';
        $column['enrollment_order_id'] = 'Order';
        $column['enrollment_email']    = 'Email';
        $column['enrollment_messages'] = 'Last Message';
        return $column;
    }

    /**
     * Fills the values of the custom columns in the list view
     *
     * @return void
     */
    public function fill_custom_columns_in_list_view( $column_name, $post_id ) {
        switch ( $column_name ) {
            case 'enrollment_status':
                if ( get_post( $post_id )->post_status === 'enrollment-success' ) {
                    echo '<b style="color:green;">Success</b>';
                }
                if ( get_post( $post_id )->post_status === 'enrollment-error' ) {
                    echo '<b style="color:red;">Error</b>';
                }
                if ( get_post( $post_id )->post_status === 'enrollment-pending' ) {
                    echo '<b style="color:orange;">Pending</b>';
                }
                break;
            case 'enrollment_type':
                if ( get_post_meta( $post_id, 'is_active', true ) ) {
                    echo 'Enroll';
                } else {
                    echo 'Unenroll';
                }
                break;
            case 'enrollment_order_id':
                $order_id = get_post_meta( $post_id, 'order_id', true );
                if ( $order_id ) {
                    echo edit_post_link( '# ' . $order_id, '<p>', '</p>', $order_id );
                }
                break;
            case 'enrollment_email':
                $email = get_post_meta( $post_id, 'email', true );
                if ( $email ) {
                    echo $email;
                }
                break;
            case 'enrollment_messages':
                echo( get_post_meta( $post_id, 'errors', true ) );
                break;
            default:
        }
    }

    /**
     * Prepare the site to work with the Enrollment object as a CPT
     *
     * @return void
     */
    public function set_up_admin() {
        // List view.
        add_filter( 'post_row_actions', array( $this, 'remove_table_row_actions' ) );
        add_filter( 'manage_posts_custom_column', array( $this, 'fill_custom_columns_in_list_view' ), 10, 3 );
        add_filter( 'manage_openedx_enrollment_posts_columns', array( $this, 'add_columns_to_list_view' ) );
    }

    /**
     * Main WP_Openedx_Enrollment Instance
     *
     * Ensures only one instance of WP_Openedx_Enrollment is loaded or can be loaded.
     *
     * @static
     * @see WP_Openedx_Enrollment()
     * @return Main WP_Openedx_Enrollment instance
     */
    public static function instance( $parent ) {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self( $parent );
        }
        return self::$_instance;
    } // End instance()

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone() {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
    } // End __clone()

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
    } // End __wakeup()

}
