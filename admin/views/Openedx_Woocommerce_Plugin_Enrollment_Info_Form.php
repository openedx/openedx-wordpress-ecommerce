<?php

namespace App\admin\views;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Openedx_Woocommerce_Plugin_Enrollment_Info_Form {

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
    public function __construct( $enrollment_request ) {
        $this->render_enrollment_info_form($enrollment_request);
        $this->replace_admin_meta_boxes();
    }

    /**
     * Print openedx enrollment edit metabox
     *
     * @param WP_Post $post Current post object.
     */
    public function render_enrollment_info_form( $post ) {

        if ( $this->post_type !== $post->post_type ) {
            return;
        }
        $post_id = $post->ID;

        $course_id = get_post_meta( $post_id, 'course_id', true );
        $email     = get_post_meta( $post_id, 'email', true );
        $mode      = get_post_meta( $post_id, 'mode', true );
        $is_active = get_post_meta( $post_id, 'is_active', true );
        $order_id  = get_post_meta( $post_id, 'order_id', true );

        $new_enrollment = false;
        if (! $course_id && ! $email) {
            $new_enrollment = true;
        }
        ?>
        <div id="namediv" class="postbox">
        <h2 class="">Open edX enrollment request</h2>
        <fieldset>
        <input type="hidden" name="new_enrollment" value="<?php echo( $new_enrollment ); ?>">
        <table class="form-table">
            <tbody>
                <tr>
                    <td class="first"><label for="openedx_enrollment_course_id">Course ID</label></td>
                    <td>
                        <input type="text" id="openedx_enrollment_course_id" name="enrollment_course_id"
                        value="<?php echo( $course_id ); ?>">
                    </td>
                </tr>
                <tr>
                    <td class="first"><label>User Email</label></td>
                    <td>
                         <div style="width: 49%; display: inline-table;">	
                             <input type="email"
                              id="openedx_enrollment_email" 
                              name="enrollment_email"	
                              title="You only need to fill one. Either the email or username"	
                              value="<?php echo( $email ); ?>">	
                         </div>
                    </td>
                </tr>
                <tr>
                    <td class="first"><label for="openedx_enrollment_mode">Course Mode</label></td>
                    <td>
                        <select id="openedx_enrollment_mode" name="enrollment_mode">
                            <option value="honor" 
                            <?php
                            if ( $mode === 'honor' ) {
                                echo( 'selected="selected"' );}
                            ?>
                            ><?php esc_html_e( 'Honor', 'wp-edunext-marketing-site' ); ?></option>
                            <option value="audit" 
                            <?php
                            if ( $mode === 'audit' ) {
                                echo( 'selected="selected"' );}
                            ?>
                            ><?php esc_html_e( 'Audit', 'wp-edunext-marketing-site' ); ?></option>
                            <option value="verified" 
                            <?php
                            if ( $mode === 'verified' ) {
                                echo( 'selected="selected"' );}
                            ?>
                            ><?php esc_html_e( 'Verified', 'wp-edunext-marketing-site' ); ?></option>
                            <option value="credit" 
                            <?php
                            if ( $mode === 'credit' ) {
                                echo( 'selected="selected"' );}
                            ?>
                            ><?php esc_html_e( 'Credit', 'wp-edunext-marketing-site' ); ?></option>
                            <option value="professional" 
                            <?php
                            if ( $mode === 'professional' ) {
                                echo( 'selected="selected"' );}
                            ?>
                            ><?php esc_html_e( 'Professional', 'wp-edunext-marketing-site' ); ?></option>
                            <option value="no-id-professional" 
                            <?php
                            if ( $mode === 'no-id-professional' ) {
                                echo( 'selected="selected"' );}
                            ?>
                            ><?php esc_html_e( 'No ID Professional', 'wp-edunext-marketing-site' ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="first"><label for="openedx_enrollment_is_active">Request Type</label></td>
                    <td>

                        <select id="openedx_enrollment_is_active" name="enrollment_request_type">
                            <option value="enroll"
                            <?php
                            if ( $is_active or $new_enrollment ) {
                                echo( 'selected="selected"' );}
                            ?>
                            ><?php esc_html_e( 'Enroll', 'wp-edunext-marketing-site' ); ?></option>
                            <option value="unenroll"
                            <?php
                            if ( ! $is_active and ! $new_enrollment ) {
                                echo( 'selected="selected"' );}
                            ?>
                            ><?php esc_html_e( 'Un-enroll', 'wp-edunext-marketing-site' ); ?></option>
                        </select>

                    </td>
                </tr>
                <tr>
                    <td class="first"><label for="openedx_enrollment_order_id">WC Order ID</label></td>
                    <td>
                        <div style="width: 30%; display: inline-table;">
                            <input type="text" id="openedx_enrollment_order_id" name="enrollment_order_id"
                            value="<?php echo( $order_id ); ?>">
                        </div>
                        <div style="width: 30%; display: inline-table;">
                            <?php edit_post_link( 'view', '<p>', '</p>', $order_id ); ?>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="first"><label>General Info</label></td>
                    <td>
                        <p>Edited: 
                        <?php
                        if ( get_post_meta( $post_id, 'edited', true ) ) {
                            echo 'yes';
                        } else {
                            echo 'no';
                        }
                        ?>
                        </p>
                        <p>Last edited: <?php echo( get_the_modified_time( '', $post_id ) . ' ' . get_the_modified_date( '', $post_id ) ); ?></p>
                    </td>
                </tr>

                <tr>
                    <td class="first"><label>Choose an Action</label></td>
                    <td>
                        <select name="enrollment_action" id="actions-select">
                            <option value="save_no_process"><?php esc_html_e( 'Save without processing', 'wp-edunext-marketing-site' ); ?></option>
                            <option value="enrollment_sync"><?php esc_html_e( 'Synchronize (pull information)', 'wp-edunext-marketing-site' ); ?></option>
                            <option value="enrollment_process" selected><?php esc_html_e( 'Process request', 'wp-edunext-marketing-site' ); ?></option>
                            <option value="enrollment_no_pre"><?php esc_html_e( 'Process no pre-enrollment', 'wp-edunext-marketing-site' ); ?></option>
                            <option value="enrollment_force"><?php esc_html_e( 'Process --force', 'wp-edunext-marketing-site' ); ?></option>
                            <option value="enrollment_no_pre_force"><?php esc_html_e( 'Process no pre-enrollment --force', 'wp-edunext-marketing-site' ); ?></option>
                        </select>
                    </td>
                </tr>
                    
                <tr>
                    <td class="first"><label>Create/Update Enrollment</label></td>
                    <td>
                        <button class="button save_order button-primary"><span><?php esc_html_e( 'Apply action', 'wp-edunext-marketing-site' ); ?></span></button>
                    </td>
                </tr>
            </tbody>
        </table>
        </fieldset>
        </div>
        <?php
    }

    /**
     * Temporary function to get test logs for the logs box
     * 
     * @return string Test logs
     */
    public function get_logs() {
        $logs = "Log 1\n";
        $logs .= "Log 2\n";
        $logs .= "Log 3\n";
        $logs .= "No errors ocurred\n";
        return $logs;
    }


     /**
     * Renders the logs box for the edit post box
     *
     * @return void
     */
    public function render_logs_box() {
        $logs = $this->get_logs();
        ?>
        <div class="logs_box">
            <pre><?php echo esc_html( $logs ); ?></pre>
        </div>
        <?php
    }

    /**
     * Replace admin meta boxes
     *
     * @return void
     */
    public function replace_admin_meta_boxes() {
        remove_meta_box( 'submitdiv', $this->post_type, 'side' );

        add_meta_box( 'openedx_enrollment_request_actions', 'Enrollment Operation Logs', array( $this, 'render_logs_box' ), $this->post_type, 'side', 'high' );
    }
    
}
