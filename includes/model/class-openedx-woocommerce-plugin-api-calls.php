<?php
/**
 * Openedx plugin API calls file.
 * Management of all API calls of Open EDX plugin.
 *
 * @category   Model
 * @package    WordPress
 * @subpackage Openedx_Woocommerce_Plugin
 * @since      1.6.0
 */

namespace App\model;

require_once plugin_dir_path( dirname( __DIR__ ) ) . 'vendor/autoload.php';
use DateTime;
use DateInterval;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\GuzzleException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This class contains all the API calls necessary for the plugin,
 * these calls include a hard error handling for all responses and requests.
 */
class Openedx_Woocommerce_Plugin_Api_Calls {


	const API_ACCESS_TOKEN    = '/oauth2/access_token';
	const API_ENROLLMENT      = '/api/enrollment/v1/enrollment';
	const API_SYNC_ENROLLMENT = '/api/enrollment/v1/enrollments';
	const API_GET_USER        = '/api/user/v1/accounts';

	/**
	 * The Guzzle HTTP client object.
	 *
	 * @var GuzzleHttp\Client
	 */
	private $client;

	/**
	 * Class constructor.
	 *
	 * Initializes the Guzzle HTTP client.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->client = new Client();
	}

	/**
	 * Generates an access token using the Open edX API credentials.
	 *
	 * Makes a POST request to the Open edX API /oauth2/access_token endpoint
	 * to generate a new JWT access token.
	 *
	 * @param string $client_id The Open edX API client ID.
	 * @param string $client_secret The Open edX API client secret.
	 * @param string $domain The Open edX domain.
	 * @return string|array The access token string, or an error array.
	 */
	public function generate_token( $client_id, $client_secret, $domain ) {

		try {
			$response = $this->client->request(
				'POST',
				$domain . self::API_ACCESS_TOKEN,
				array(
					'form_params' => array(
						'client_id'     => $client_id,
						'client_secret' => $client_secret,
						'grant_type'    => 'client_credentials',
						'token_type'    => 'jwt',
					),
				)
			);

			$status_code   = $response->getStatusCode();
			$response_data = json_decode( $response->getBody(), true );
			return array( 'success', $response_data );

		} catch ( RequestException $e ) {
			return $this->handle_request_error( $e );
		} catch ( GuzzleException $e ) {
			return array( 'error', $e->getMessage() );
		}
	}

	/**
	 * Handles the errors in every API process or request to keep the user updated.
	 *
	 * @param RequestException $e The exception object.
	 * @return array The error array.
	 */
	public function handle_request_error( $e ) {

		if ( $e->hasResponse() ) {
			$status_code = $e->getResponse()->getStatusCode();
			$error_data  = $e->getResponse()->getBody()->getContents();

			if ( isset( json_decode( $error_data, true )['error'] ) ) {
				return array( 'error', 'Error: ' . $status_code . ' - ' . json_decode( $error_data, true )['error'] );
			} elseif ( isset( json_decode( $error_data, true )['message'] ) ) {
				return array( 'error', 'Error: ' . $status_code . ' - ' . json_decode( $error_data, true )['message'] );
			} else {
				return array( 'error', 'Error: ' . $status_code . ' - Please review the enrollment form information.' );
			}
		}
	}

	/**
	 * Decide how the process has to do the API request depending on the selected action.
	 *
	 * @param string $enrollment_data The enrollment data.
	 * @param string $enrollment_action The enrollment action.
	 * @return array The response array.
	 */
	public function enrollment_send_request( $enrollment_data, $enrollment_action ) {

		if ( 'save_no_process' === $enrollment_action ) {

			return array( 'not_api', 'This action does not require an API call.' );
		}

		$access_token = $this->check_access_token();
		$user         = $this->get_user( $enrollment_data['enrollment_email'], $access_token );

		if ( 'error' === $access_token[0] || 'error' === $user[0] ) {
			if ( 'error' === $access_token[0] && 'error' === $user[0] ) {
				return array( 'error', 'Error(s) getting access_token and user: ' . $access_token[1] . ' - ' . $user[1] );
			} elseif ( 'error' === $access_token[0] ) {
				return array( 'error', 'Error(s) getting access_token: ' . $access_token[1] );
			} else {
				return array( 'error', 'Error(s) getting user: ' . $user[1] );
			}
		}

		$course_id    = $enrollment_data['enrollment_course_id'];
		$course_mode  = $enrollment_data['enrollment_mode'];
		$request_type = $enrollment_data['enrollment_request_type'];

		if ( 'enroll' === $request_type ) {

			if ( 'enrollment_process' === $enrollment_action ) {
				$method = 'POST';
				$body   = array(
					'user'                  => $user[1],
					'mode'                  => strtolower( $course_mode ),
					'course_details'        => array(
						'course_id' => $course_id,
					),
					'enrollment_attributes' => array(
						array(
							'namespace' => 'openedx-woocommerce-plugin',
							'name'      => 'message',
							'value'     => 'Enrollment request response.',
						),
					),
				);

				return $this->enrollment_request_api_call( $method, $body, $access_token );

			} elseif ( 'enrollment_no_pre' === $enrollment_action ) {
				// This space is for the no pre-enrollment call.
				return array( 'error', 'This feature is not implemented yet.' );
			} elseif ( 'enrollment_force' === $enrollment_action ) {
				$method = 'POST';
				$body   = array(
					'user'                  => $user[1],
					'mode'                  => strtolower( $course_mode ),
					'course_details'        => array(
						'course_id' => $course_id,
					),
					'enrollment_attributes' => array(
						array(
							'namespace' => 'openedx-woocommerce-plugin',
							'name'      => 'message',
							'value'     => 'Enrollment request response.',
						),
					),
					'force_enrollment'      => true,
				);

				return $this->enrollment_request_api_call( $method, $body, $access_token );

			} elseif ( 'enrollment_no_pre_force' === $enrollment_action ) {
				// This space is for the no pre-enrollment call.
				return array( 'error', 'This feature is not implemented yet.' );
			}
		} elseif ( 'unenroll' === $request_type ) {

			if ( 'enrollment_process' === $enrollment_action ) {
				$method = 'POST';
				$body   = array(
					'user'                  => $user[1],
					'mode'                  => strtolower( $course_mode ),
					'course_details'        => array(
						'course_id' => $course_id,
					),
					'enrollment_attributes' => array(
						array(
							'namespace' => 'openedx-woocommerce-plugin',
							'name'      => 'message',
							'value'     => 'Enrollment request response.',
						),
					),
					'is_active'             => false,
				);

				return $this->enrollment_request_api_call( $method, $body, $access_token );

			} elseif ( 'enrollment_no_pre' === $enrollment_action ) {
				// This space is for the no pre-enrollment call.
				return array( 'error', 'This feature is not implemented yet.' );
			} elseif ( 'enrollment_force' === $enrollment_action ) {
				$method = 'POST';
				$body   = array(
					'user'                  => $user[1],
					'mode'                  => strtolower( $course_mode ),
					'course_details'        => array(
						'course_id' => $course_id,
					),
					'enrollment_attributes' => array(
						array(
							'namespace' => 'openedx-woocommerce-plugin',
							'name'      => 'message',
							'value'     => 'Enrollment request response.',
						),
					),
					'force_enrollment'      => true,
					'is_active'             => false,
				);

				return $this->enrollment_request_api_call( $method, $body, $access_token );

			} elseif ( 'enrollment_no_pre_force' === $enrollment_action ) {
				// This space is for the no pre-enrollment call.
				return array( 'error', 'This feature is not implemented yet.' );
			}
		}

		if ( 'enrollment_sync' === $enrollment_action ) {

			$method = 'GET';
			$body   = array(
				'username'  => $user[1],
				'course_id' => str_replace( '+', '%2B', $course_id ),
			);

			return $this->enrollment_sync_request( $method, $body, $access_token );
		}
	}

	/**
	 * Performs a request to the Open edX API endpoint
	 *
	 * @param string $method The HTTP method to use.
	 * @param array  $body The request body.
	 * @param string $access_token The access token.
	 *
	 * @return array The response array.
	 */
	public function enrollment_request_api_call( $method, $body, $access_token ) {

		$domain = get_option( 'openedx-domain' );

		try {

			$response = $this->client->request(
				$method,
				$domain . self::API_ENROLLMENT,
				array(
					'headers' => array(
						'Authorization' => 'JWT ' . $access_token,
						'Content-Type'  => 'application/json',
					),
					'json'    => $body,
				),
			);

			$status_code   = $response->getStatusCode();
			$response_data = $response->getBody();
			return array( 'success', $response_data );
		} catch ( RequestException $e ) {
			return $this->handle_request_error( $e );
		} catch ( GuzzleException $e ) {
			return array( 'error', $e->getMessage() );
		}
	}

	/**
	 * API call for synchronization requests.
	 *
	 * @param string $method The HTTP method to use.
	 * @param array  $body The request body.
	 * @param string $access_token The access token.
	 *
	 * @return array The response array.
	 */
	public function enrollment_sync_request( $method, $body, $access_token ) {

		$domain = get_option( 'openedx-domain' );
		$url    = $domain . self::API_SYNC_ENROLLMENT . '?username=' . $body['username'] . '&course_id=' . $body['course_id'];

		try {

			$response = $this->client->request(
				$method,
				$url,
				array(
					'headers' => array(
						'Authorization' => 'JWT ' . $access_token,
						'Content-Type'  => 'application/json',
					),
				),
			);

			$status_code   = $response->getStatusCode();
			$response_data = $response->getBody();
			return array( 'success', $response_data );
		} catch ( RequestException $e ) {
			return $this->handle_request_error( $e );
		} catch ( GuzzleException $e ) {
			return array( 'error', $e->getMessage() );
		}
	}

	/**
	 * Check the access token validity.
	 *
	 * @return string|array The access token string, or an error array.
	 */
	public function check_access_token() {

		$current_token_exp = get_option( 'openedx-token-expiration' );
		$current_token_exp = $current_token_exp->sub( new DateInterval( 'PT300S' ) );
		$current_date      = new DateTime();

		if ( $current_date >= $current_token_exp ) {

			$client_id     = get_option( 'openedx-client-id' );
			$client_secret = get_option( 'openedx-client-secret' );
			$domain        = get_option( 'openedx-domain' );

			$new_token = $this->generate_token( $client_id, $client_secret, $domain );

			if ( 'error' === $new_token[0] ) {
				return $new_token;
			} else {
				return $this->set_new_token( $new_token, $current_date );
			}
		} elseif ( $current_date < $current_token_exp ) {
			return get_option( 'openedx-jwt-token' );
		}
	}

	/**
	 * Set the new token generated in check_access_token method
	 *
	 * @param string $new_token The Open edX API client ID.
	 * @param string $current_date The Open edX API client secret.
	 *
	 * @return string|array The access token string, or an error array.
	 */
	public function set_new_token( $new_token, $current_date ) {

		$response_data = $new_token[1];
		update_option( 'openedx-jwt-token', $response_data['access_token'] );

		$exp_time     = $response_data['expires_in'];
		$new_exp_date = $current_date->add( new DateInterval( 'PT' . $exp_time . 'S' ) );
		update_option( 'openedx-token-expiration', $new_exp_date );

		return array( 'success', $response_data['access_token'] );
	}

	/**
	 * Send a request to get the username based on the provided user email.
	 *
	 * @param string $email The user email.
	 * @param string $access_token The access token.
	 *
	 * @return string|array The username string, or an error array.
	 */
	public function get_user( $email, $access_token ) {

		$domain = get_option( 'openedx-domain' );

		try {
			$response = $this->client->request(
				'GET',
				$domain . self::API_GET_USER,
				array(
					'headers' => array(
						'Authorization' => 'JWT ' . $access_token,
					),
					'query'   => array(
						'email' => $email,
					),
				),
			);

			$status_code   = $response->getStatusCode();
			$response_data = json_decode( $response->getBody(), true );
			return array( 'success', $response_data[0]['username'] );

		} catch ( RequestException $e ) {
			return $this->handle_request_error( $e );
		} catch ( GuzzleException $e ) {
			return array( 'error', $e->getMessage() );
		}
	}
}
