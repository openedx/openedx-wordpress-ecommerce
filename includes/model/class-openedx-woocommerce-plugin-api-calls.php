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


	// API constants for the Open edX API calls.
	const API_ACCESS_TOKEN    = '/oauth2/access_token';
	const API_ENROLLMENT      = '/api/enrollment/v1/enrollment';
	const API_SYNC_ENROLLMENT = '/api/enrollment/v1/enrollments';
	const API_GET_USER        = '/api/user/v1/accounts';

	// API constants for new endpoints available for Open edX API calls.
	const API_ENROLLMENT_ALLOWED      = '/api/enrollment/v1/enrollment_allowed/';
	const API_ENROLLMENT_ALLOWED_SYNC = '/api/enrollment/v1/enrollment_allowed';

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
				return array( 'error', $status_code . ': ' . json_decode( $error_data, true )['error'], $status_code );
			} elseif ( isset( json_decode( $error_data, true )['message'] ) ) {
				return array( 'error', $status_code . ': ' . json_decode( $error_data, true )['message'], $status_code );
			} else {
				return array( 'error', $status_code . ': ' . $e->getMessage() );
			}
		}
	}

	/**
	 * Decide how the process has to do the API request depending on the new endpoints response.
	 * If the response works, it will return the response, if not, it will try and return the
	 * old endpoints response.
	 *
	 * @param string $enrollment_data The enrollment data.
	 * @param string $enrollment_action The enrollment action.
	 * @return array The response array.
	 */
	public function enrollment_handle_old_or_new( $enrollment_data, $enrollment_action ) {

		$response_new = $this->enrollment_send_request_new_endpoints( $enrollment_data, $enrollment_action );
		$status_code  = '';

		if ( 'error' === $response_new[0] ) {
			$status_code = strval( $response_new[2] );
		}

		if ( ( 'enrollment_allowed' === $enrollment_action || 'enrollment_allowed_force' === $enrollment_action ) && '409' === $status_code ) {

			return $response_new;
		} elseif ( ( 'enrollment_allowed' !== $enrollment_action || 'enrollment_allowed_force' !== $enrollment_action ) && '409' !== $status_code ) {
			if ( 'success' === $response_new[0] ) {
				return $response_new;
			} else {
				return $this->enrollment_send_request( $enrollment_data, $enrollment_action );
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

		$access_token        = $this->check_access_token();
		$access_token_string = $this->get_access_token( $access_token );
		$user                = $this->check_if_user_exists( $enrollment_data['enrollment_email'], $access_token_string );

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

				return $this->enrollment_request_api_call( $method, $body, $access_token_string );

			} elseif ( 'enrollment_allowed' === $enrollment_action ) {

				return array( 'error', 'This feature is only supported by Open edX versions equal to or higher than Quince.' );

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

				return $this->enrollment_request_api_call( $method, $body, $access_token_string );

			} elseif ( 'enrollment_allowed_force' === $enrollment_action ) {

				return array( 'error', 'This feature is only supported by Open edX versions equal to or higher than Quince.' );
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

				return $this->enrollment_request_api_call( $method, $body, $access_token_string );

			} elseif ( 'enrollment_allowed' === $enrollment_action ) {

				return array( 'error', 'This feature is only supported by Open edX versions equal to or higher than Quince.' );

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

				return $this->enrollment_request_api_call( $method, $body, $access_token_string );

			} elseif ( 'enrollment_allowed_force' === $enrollment_action ) {

				return array( 'error', 'This feature is only supported by Open edX versions equal to or higher than Quince.' );

			}
		}

		if ( 'enrollment_sync' === $enrollment_action ) {

			$method = 'GET';
			$body   = array(
				'username'  => $user[1],
				'course_id' => str_replace( '+', '%2B', $course_id ),
			);

			return $this->enrollment_sync_request( $method, $body, $access_token_string, 'username' );
		}
	}

	/**
	 * Send the enroll and unenroll requests to the new endpoints using directly the user email.
	 *
	 * @param string $enrollment_data The enrollment data.
	 * @param string $enrollment_action The enrollment action.
	 * @return array The response array.
	 */
	public function enrollment_send_request_new_endpoints( $enrollment_data, $enrollment_action ) {

		if ( 'save_no_process' === $enrollment_action ) {

			return array( 'not_api', 'This action does not require an API call.' );
		}

		$access_token        = $this->check_access_token();
		$access_token_string = $this->get_access_token( $access_token );

		$course_id        = $enrollment_data['enrollment_course_id'];
		$course_mode      = $enrollment_data['enrollment_mode'];
		$request_type     = $enrollment_data['enrollment_request_type'];
		$enrollment_email = $enrollment_data['enrollment_email'];

		if ( 'enroll' === $request_type ) {

			if ( 'enrollment_process' === $enrollment_action ) {

				$method = 'POST';
				$body   = array(
					'email'                 => $enrollment_email,
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

				return $this->enrollment_request_api_call( $method, $body, $access_token_string );

			} elseif ( 'enrollment_allowed' === $enrollment_action ) {

				return $this->enrollment_allowed_checks( $enrollment_data, $access_token_string );

			} elseif ( 'enrollment_force' === $enrollment_action ) {
				$method = 'POST';
				$body   = array(
					'email'                 => $enrollment_email,
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

				return $this->enrollment_request_api_call( $method, $body, $access_token_string );

			} elseif ( 'enrollment_allowed_force' === $enrollment_action ) {

				return $this->enrollment_allowed_checks( $enrollment_data, $access_token_string, true );
			}
		} elseif ( 'unenroll' === $request_type ) {

			if ( 'enrollment_process' === $enrollment_action ) {
				$method = 'POST';
				$body   = array(
					'user'                  => $enrollment_email,
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

				return $this->enrollment_request_api_call( $method, $body, $access_token_string );

			} elseif ( 'enrollment_allowed' === $enrollment_action ) {

				return $this->unenrollment_allowed_checks( $enrollment_data, $access_token_string );

			} elseif ( 'enrollment_force' === $enrollment_action ) {
				$method = 'POST';
				$body   = array(
					'user'                  => $enrollment_email,
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

				return $this->enrollment_request_api_call( $method, $body, $access_token_string );

			} elseif ( 'enrollment_allowed_force' === $enrollment_action ) {

				return $this->unenrollment_allowed_checks( $enrollment_data, $access_token_string, true );
			}
		}

		if ( 'enrollment_sync' === $enrollment_action ) {

			$method = 'GET';
			$body   = array(
				'email'     => $enrollment_email,
				'course_id' => str_replace( '+', '%2B', $course_id ),
			);

			return $this->enrollment_sync_request( $method, $body, $access_token_string, 'email' );
		}
	}

	/**
	 * Create the request to the new endpoint for enrollment allowed.
	 *
	 * @param string $enrollment_data The enrollment data.
	 * @param string $access_token_string The access token.
	 * @param string $force If the request is forced or not.
	 * @return array The response array.
	 */
	public function enrollment_allowed_checks( $enrollment_data, $access_token_string, $force = false ) {

		$course_id        = $enrollment_data['enrollment_course_id'];
		$enrollment_email = $enrollment_data['enrollment_email'];
		$user_exist       = $this->check_if_user_exists( $enrollment_email, $access_token_string );

		if ( 'success' !== $user_exist[0] ) {

			$method = 'POST';

			if ( ! $force ) {
				$body = array(
					'email'       => $enrollment_email,
					'course_id'   => $course_id,
					'auto_enroll' => true,
				);
			} else {
				$body = array(
					'email'            => $enrollment_email,
					'course_id'        => $course_id,
					'auto_enroll'      => true,
					'force_enrollment' => true,
				);
			}

			return $this->enrollment_allowed_request( $method, $body, $access_token_string );

		} else {

			return $this->enrollment_handle_old_or_new( $enrollment_data, 'enrollment_process' );

		}
	}

	/**
	 * Create the request to the new endpoint for unenrollment with enrollment_allowed enabled.
	 *
	 * @param string $enrollment_data The enrollment data.
	 * @param string $access_token_string The access token.
	 * @param string $force If the request is forced or not.
	 */
	public function unenrollment_allowed_checks( $enrollment_data, $access_token_string, $force = false ) {

		$course_id        = $enrollment_data['enrollment_course_id'];
		$enrollment_email = $enrollment_data['enrollment_email'];
		$user_exist       = $this->check_if_user_exists( $enrollment_email, $access_token_string );

		if ( 'success' !== $user_exist[0] ) {

			$method = 'DELETE';

			if ( ! $force ) {
				$body = array(
					'email'     => $enrollment_email,
					'course_id' => $course_id,
				);
			} else {
				$body = array(
					'email'            => $enrollment_email,
					'course_id'        => $course_id,
					'force_enrollment' => true,
				);
			}

			$response = $this->enrollment_allowed_request( $method, $body, $access_token_string );

			if ( 'success' === $response[0] ) {
				return array( 'success', wp_json_encode( 'User unenrolled successfully.' ) );
			} else {
				return $response;
			}
		} else {

			return $this->enrollment_handle_old_or_new( $enrollment_data, 'enrollment_process' );

		}
	}

	/**
	 * Check if a user exists in the Open edX platform using its email.
	 *
	 * @param string $enrollment_email The user email.
	 * @param string $access_token_string The access token.
	 * @return array The response array.
	 */
	public function check_if_user_exists( $enrollment_email, $access_token_string ) {

		$user = $this->get_user( $enrollment_email, $access_token_string );
		return $user;
	}

	/**
	 * Performs a request to the enrollment_allowed Open edX API endpoint.
	 *
	 * @param string $method The HTTP method to use.
	 * @param array  $body The request body.
	 * @param string $access_token_string The access token.
	 * @return array The response array.
	 */
	public function enrollment_allowed_request( $method, $body, $access_token_string ) {

		$domain = get_option( 'openedx-domain' );

		try {

			$response = $this->client->request(
				$method,
				$domain . self::API_ENROLLMENT_ALLOWED,
				array(
					'headers' => array(
						'Authorization' => 'JWT ' . $access_token_string,
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
	 * Performs a request to the Open edX API endpoint
	 *
	 * @param string $method The HTTP method to use.
	 * @param array  $body The request body.
	 * @param string $access_token_string The access token.
	 *
	 * @return array The response array.
	 */
	public function enrollment_request_api_call( $method, $body, $access_token_string ) {

		$domain = get_option( 'openedx-domain' );

		try {

			$response = $this->client->request(
				$method,
				$domain . self::API_ENROLLMENT,
				array(
					'headers' => array(
						'Authorization' => 'JWT ' . $access_token_string,
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
	 * @param string $access_token_string The access token.
	 * @param string $user_filter User filter to know if it's using email or username.
	 *
	 * @return array The response array.
	 */
	public function enrollment_sync_request( $method, $body, $access_token_string, $user_filter ) {

		$domain = get_option( 'openedx-domain' );
		$url    = $domain . self::API_SYNC_ENROLLMENT . '?' . $user_filter . '=' . $body[ $user_filter ] . '&course_id=' . $body['course_id'];

		try {

			$response = $this->client->request(
				$method,
				$url,
				array(
					'headers' => array(
						'Authorization' => 'JWT ' . $access_token_string,
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

		$current_token_exp = get_option( 'openedx-token-expiration-overlap' );
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
		update_option( 'openedx-token-expiration-overlap', $new_exp_date );

		return array( 'success', $response_data['access_token'] );
	}

	/**
	 * Send a request to get the username based on the provided user email.
	 *
	 * @param string $email The user email.
	 * @param string $access_token_string The access token.
	 *
	 * @return string|array The username string, or an error array.
	 */
	public function get_user( $email, $access_token_string ) {

		$domain = get_option( 'openedx-domain' );

		try {
			$response = $this->client->request(
				'GET',
				$domain . self::API_GET_USER,
				array(
					'headers' => array(
						'Authorization' => 'JWT ' . $access_token_string,
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

	/**
	 * // The access token can be an array or not; if it's an array, we need the value at index 1, which contains the generated token.
	 *
	 * @param string $access_token_string The access token.
	 * @return string The access token string.
	 */
	public function get_access_token( $access_token_string ) {

		if ( 'array' === gettype( $access_token_string ) ) {
			return $access_token_string[1];
		} else {
			return $access_token_string;
		}
	}
}
