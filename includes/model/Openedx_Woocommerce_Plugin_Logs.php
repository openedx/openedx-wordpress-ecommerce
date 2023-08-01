<?php

namespace App\model;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Openedx_Woocommerce_Plugin_Log {

    /**
     * Insert a new log entry into the database.
     *
     * @param int $post_id The post ID.
     * @param array $old_data_array The old data array.
     * @param array $enrollment_arr The new data array.
     * @param string $enrollment_action The API action to perform.
     * @return void
     */
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
    
        $date = current_time('mysql', true);
        $user_id = get_current_user_id(); 
        $username = get_user_by('id', $user_id)->user_login; 
    
        if (empty($old_data_array['enrollment_course_id'])) {
            $enrollment_action = 'Object Created';
        } 
    
        if (empty($old_data_array['enrollment_course_id'])) {
            $old_data_array = '--';
        }
    
        $log_data = array(
            'post_id' => $post_id,
            'mod_date' => $date,
            'user' => $username,
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
     * Get logs from the database for a given post.
     *
     * @param int $post_id The post ID.
     * @return string $formatted_logs Logs formatted as HTML.
     */
    public function getLogs($post_id)
    {
        global $wpdb;
        $tabla_logs = $wpdb->prefix . 'enrollment_logs_req_table';

        $logs = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $tabla_logs WHERE post_id = %d ORDER BY mod_date DESC",
                $post_id
            ),
            ARRAY_A
        );

        $formatted_logs = '';
        foreach ($logs as $log) {
            $formatted_logs .= "<div class='log_entry'>";
            $formatted_logs .= "<strong>Timestamp:</strong> " . date('d-m-Y H:i:s', strtotime($log['mod_date'])) . "<br>";
            $formatted_logs .= "<strong>User:</strong> " . $log['user'] . "<br>";
            $formatted_logs .= "<strong>Action:</strong> " . $log['action_name'] . "<br>";
            $formatted_logs .= "<strong>Object Before:</strong> " . $log['object_before'] . "<br>";
            $formatted_logs .= "<strong>Object After:</strong> " . $log['object_after'] . "<br>";
            $formatted_logs .= "</div>";
        }

        return $formatted_logs;
    }
}