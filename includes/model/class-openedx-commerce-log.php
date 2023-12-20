<?php
/**
 * This file contains every method to create logs for
 * enrollment operations and save them in the WordPress database.
 *
 * @category   Model
 * @package    WordPress
 * @subpackage Openedx_Commerce
 * @since      1.4.0
 */

namespace OpenedXCommerce\model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This class contains every functions to process
 * logs that can be created by the plugin.
 */
class Openedx_Commerce_Log {
	/**
	 * Creates a new log entry in the database.
	 *
	 * @param int    $post_id The post ID.
	 * @param array  $old_data_array The old data array.
	 * @param array  $enrollment_arr The new data array.
	 * @param string $enrollment_action The API action to perform.
	 * @param array  $api_response The API response.
	 *
	 * @return array $log_data The log data.
	 */
	public function create_change_log( $post_id, $old_data_array, $enrollment_arr, $enrollment_action, $api_response ) {

		try {
			global $wpdb;
			$logs_table = $wpdb->prefix . 'enrollment_logs_req_table';
			$new_post   = get_post( $post_id );

			$date     = current_time( 'mysql', true );
			$user_id  = get_current_user_id();
			$username = get_user_by( 'id', $user_id )->user_login;
			$response = $this->check_api_response( $api_response );

			$log_data = array(
				'post_id'       => $post_id,
				'mod_date'      => $date,
				'user'          => $username,
				'action_name'   => $enrollment_action,
				'object_before' => wp_json_encode( $old_data_array ),
				'object_after'  => wp_json_encode( $enrollment_arr ),
				'api_response'  => $response,
			);

			if ( empty( $old_data_array['openedx_enrollment_course_id'] ) ) {
				$log_data['object_status'] = 'Object Created';
				$old_data_array            = '--';
			} else {
				$log_data['object_status'] = 'Object Modified';
			}

			$wpdb->insert( $logs_table, $log_data ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery

		} catch ( Exception $e ) {
			return array( 'error', 'An error occurred creating change log: ' . $e->getMessage() );
		}
	}

	/**
	 * Check the API response from the Open edX API.
	 *
	 * @param array $response The API response.
	 *
	 * @return string $response The API response.
	 */
	public function check_api_response( $response ) {

		$response_status  = $response[0];
		$response_message = $response[1];

		switch ( $response_status ) {

			case 'error':
				return $response_message;

			case 'success':
				// Convert the response to JSON to be able to display it in the log.
				// The json_decode is needed to convert the response (string) to an asociative array.
				// The json_encode is needed to convert the asociative array to a JSON and be able to access the variables.
				return wp_json_encode( json_decode( $response_message, true ) );

			case 'not_api':
				return $response_message;

			case 'no_process':
				return $response_message;
		}
	}

	/**
	 * Get logs from the database for a given post.
	 *
	 * @param int $post_id The post ID.
	 * @return string $formatted_logs Logs formatted as HTML.
	 */
	public function get_logs( $post_id ) {

		global $wpdb;

		try {

			$cache_key = 'logs_for_post_' . $post_id;
			$logs      = wp_cache_get( $cache_key );

			if ( false === $logs ) {
				$logs = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM wp_enrollment_logs_req_table WHERE post_id = %d ORDER BY mod_date DESC', $post_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				wp_cache_set( $cache_key, $logs, 60 * 60 );
			}

			$formatted_logs = '';
			foreach ( $logs as $log ) {

				if ( null !== json_decode( $log['api_response'], true ) ) {
					$formatted_logs .= "<div class='openedx_log_entry_api'>";
				} else {
					$formatted_logs .= "<div class='openedx_log_entry_api_error'>";
				}

				$formatted_logs .= '<strong>API:</strong> ' . $log['api_response'] . '<br>';
				$formatted_logs .= '</div>';
				$formatted_logs .= "<div class='openedx_log_entry'>";
				$formatted_logs .= '<strong>Timestamp:</strong> ' . gmdate( 'd-m-Y H:i:s', strtotime( $log['mod_date'] ) ) . '<br>';
				$formatted_logs .= '<strong>User:</strong> ' . $log['user'] . '<br>';
				$formatted_logs .= '<strong>Action:</strong> ' . $log['action_name'] . '<br>';
				$formatted_logs .= '<strong>Status:</strong> ' . $log['object_status'] . '<br>';
				$formatted_logs .= '<strong>Object Before:</strong> ' . $log['object_before'] . '<br>';
				$formatted_logs .= '<strong>Object After:</strong> ' . $log['object_after'] . '<br>';
				$formatted_logs .= '</div>';
			}

			return $formatted_logs;
		} catch ( Exception $e ) {
			return 'Error occurred while fetching logs. Please try again later.';
		}
	}
}
