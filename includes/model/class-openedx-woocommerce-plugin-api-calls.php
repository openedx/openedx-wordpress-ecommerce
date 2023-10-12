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
				return array( 'error', $status_code . ': ' . $e->getMessage(), $status_code );
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
	public function request_handler( $enrollment_data, $enrollment_action ) {

		$request_type        = $enrollment_data['enrollment_request_type'];
		$access_token        = $this->check_access_token();
		$access_token_string = $this->get_access_token( $access_token );

		if ( 'enroll' === $request_type ) {

			if ( 'enrollment_process' === $enrollment_action ) {

				$request_with_email_body = $this->get_enrollment_process_body( $enrollment_data, false, $access_token_string, $enrollment_action );
				return $this->process_enrollment_action( $enrollment_data, $enrollment_action, $access_token_string, $request_with_email_body, self::API_ENROLLMENT, 'POST' );

			} elseif ( 'enrollment_allowed' === $enrollment_action ) {

				$enrollment_allowed_request_body = $this->get_enrollment_allowed_body( $enrollment_data, $access_token_string, $enrollment_action );

				if ( 'user_exists' === $enrollment_allowed_request_body[0] ) {
					return $this->request_handler( $enrollment_data, 'enrollment_process' );
				} else {
					return $this->process_enrollment_action( $enrollment_data, $enrollment_action, $access_token_string, $enrollment_allowed_request_body, self::API_ENROLLMENT_ALLOWED, 'POST' );
				}
			} elseif ( 'enrollment_force' === $enrollment_action ) {

				$request_with_email_body = $this->get_enrollment_process_body( $enrollment_data, false, $access_token_string, $enrollment_action );
				return $this->process_enrollment_action( $enrollment_data, $enrollment_action, $access_token_string, $request_with_email_body, 'POST' );
			} elseif ( 'enrollment_allowed_force' === $enrollment_action ) {

				$enrollment_allowed_request_body = $this->get_enrollment_allowed_body( $enrollment_data, $access_token_string, $enrollment_action );

				if ( 'user_exists' === $enrollment_allowed_request_body[0] ) {
					return $this->request_handler( $enrollment_data, 'enrollment_process' );
				} else {
					return $this->process_enrollment_action( $enrollment_data, $enrollment_action, $access_token_string, $enrollment_allowed_request_body, self::API_ENROLLMENT_ALLOWED, 'POST' );
				}
			}
		} elseif ( 'unenroll' === $request_type ) {

			if ( 'enrollment_process' === $enrollment_action ) {

				$request_with_email_body = $this->get_enrollment_process_body( $enrollment_data, false, $access_token_string, $enrollment_action );
				return $this->process_enrollment_action( $enrollment_data, $enrollment_action, $access_token_string, $request_with_email_body, self::API_ENROLLMENT, 'POST' );

			} elseif ( 'enrollment_allowed' === $enrollment_action ) {

				$enrollment_allowed_request_body = $this->get_enrollment_allowed_body( $enrollment_data, $access_token_string, $enrollment_action );

				if ( 'user_exists' === $enrollment_allowed_request_body[0] ) {
					return $this->request_handler( $enrollment_data, 'enrollment_process' );
				} else {
					return $this->unenroll_enrollment_allowed( $enrollment_data, $access_token_string, $enrollment_allowed_request_body );
				}
			} elseif ( 'enrollment_force' === $enrollment_action ) {

				$request_with_email_body = $this->get_enrollment_process_body( $enrollment_data, false, $access_token_string, $enrollment_action );
				return $this->process_enrollment_action( $enrollment_data, $enrollment_action, $access_token_string, $request_with_email_body, 'POST' );
			} elseif ( 'enrollment_allowed_force' === $enrollment_action ) {

				$enrollment_allowed_request_body = $this->get_enrollment_allowed_body( $enrollment_data, $access_token_string, $enrollment_action );

				if ( 'user_exists' === $enrollment_allowed_request_body[0] ) {
					return $this->request_handler( $enrollment_data, 'enrollment_process' );
				} else {
					return $this->unenroll_enrollment_allowed( $enrollment_data, $access_token_string, $enrollment_allowed_request_body );
				}
			}
		}

		if ( 'enrollment_sync' === $enrollment_action ) {

			$enrollment_email = $enrollment_data['enrollment_email'];
			$course_id        = $enrollment_data['enrollment_course_id'];
			$user_exist       = $this->check_if_user_exists( $enrollment_email, $access_token_string );

			if ( 'success' === $user_exist[0] ) {
				$method = 'GET';
				$body   = array(
					'username'  => $user_exist[1],
					'course_id' => str_replace( '+', '%2B', $course_id ),
				);
				return $this->enrollment_sync_request( self::API_SYNC_ENROLLMENT, $method, $body, $access_token_string, 'username' );
			} else {

				$get_enrollments_allowed = $this->get_user_enrollments_allowed( $enrollment_email, $access_token_string );

				if ( 'error' === $get_enrollments_allowed[0] ) {
					return $get_enrollments_allowed;
				}

				$enrollments_allowed_data        = json_decode( $get_enrollments_allowed[1], true );
				$course_exists                   = false;
				$get_enrollment_allowed_response = array();

				foreach ( $enrollments_allowed_data as $enrollment_allowed ) {
					if ( isset( $enrollment_allowed['course_id'] ) && $course_id === $enrollment_allowed['course_id'] ) {
						$course_exists                     = true;
						$get_enrollment_allowed_response[] = $enrollment_allowed;
					}
				}

				if ( true === $course_exists ) {
					return array( 'success', wp_json_encode( $get_enrollment_allowed_response ) );
				} else {
					return array( 'error', 'There are no enrollments allowed for the user ' . $enrollment_email . ' and course ' . $course_id );
				}
			}
		}
	}

	/**
	 * API call for synchronization requests.
	 *
	 * @param string $api_endpoint The API endpoint.
	 * @param string $method The HTTP method to use.
	 * @param array  $body The request body.
	 * @param string $access_token_string The access token.
	 * @param string $user_filter User filter to know if it's using email or username.
	 *
	 * @return array The response array.
	 */
	public function enrollment_sync_request( $api_endpoint, $method, $body, $access_token_string, $user_filter ) {

		$domain = get_option( 'openedx-domain' );
		$url    = $domain . $api_endpoint . '?' . $user_filter . '=' . $body[ $user_filter ] . '&course_id=' . $body['course_id'];

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
	 * Process to unenroll a user from a course using the enrollment allowed endpoint.
	 *
	 * @param string $enrollment_data The enrollment data.
	 * @param string $access_token_string The access token.
	 * @param string $enrollment_allowed_request_body The enrollment allowed request body.
	 * @return array The response array.
	 */
	public function unenroll_enrollment_allowed( $enrollment_data, $access_token_string, $enrollment_allowed_request_body ) {

		$course_id               = $enrollment_data['enrollment_course_id'];
		$enrollment_email        = $enrollment_data['enrollment_email'];
		$get_enrollments_allowed = $this->get_user_enrollments_allowed( $enrollment_email, $access_token_string );

		if ( 'error' === $get_enrollments_allowed[0] ) {
			return $get_enrollments_allowed;
		}

		$user_enrollment_allowed_exists = $this->check_user_enrollment_allowed_exists( $get_enrollments_allowed, $course_id );

		if ( $user_enrollment_allowed_exists ) {

			$response = $this->process_enrollment_action( $enrollment_data, $enrollment_action, $access_token_string, $enrollment_allowed_request_body, self::API_ENROLLMENT_ALLOWED, 'DELETE' );
			if ( 'success' === $response[0] ) {
				return array( 'success', wp_json_encode( 'User unenrolled successfully.' ) );
			} else {
				return $response;
			}
		} else {
			return array( 'error', 'An enrollment allowed with email ' . $enrollment_email . ' and course' . $course_id . " doesn't exists." );
		}
	}
	/**
	 * This function, in case of any enrollment action, will check the answer. If the answer is success,
	 * it will return the response, if not, it will try to do the request with the username.
	 *
	 * @param string $response The response array.
	 * @param string $enrollment_data The enrollment data.
	 * @param string $enrollment_action The enrollment action.
	 * @param string $access_token_string The access token.
	 * @return array The response array.
	 */
	public function request_response_handler( $response, $enrollment_data, $enrollment_action, $access_token_string ) {

		if ( 'success' === $response[0] ) {

			return $response;
		} elseif ( 'success' !== $response[0] ) {

			if ( ( 'enrollment_allowed' === $enrollment_action || 'enrollment_allowed_force' === $enrollment_action ) ) {
				return $this->enrollment_allowed_handler( $response, $enrollment_data, $enrollment_action, $access_token_string );
			} else {
				return $this->response_or_request_with_username( $response, $enrollment_data, $enrollment_action, $access_token_string );
			}
		}
	}

	/**
	 * This function, in case that the response handler fails, will try to do the request with the username and return the response.
	 *
	 * @param string $response The response array.
	 * @param string $enrollment_data The enrollment data.
	 * @param string $enrollment_action The enrollment action.
	 * @param string $access_token_string The access token.
	 * @return array The response array.
	 */
	public function response_or_request_with_username( $response, $enrollment_data, $enrollment_action, $access_token_string ) {

		$request_with_user_body = $this->get_enrollment_process_body( $enrollment_data, true, $access_token_string, $enrollment_action );

		if ( 'error' === $request_with_user_body[0] ) {
			return $request_with_user_body;
		} else {
			$request_with_user_res = $this->enrollment_request_api_call( self::API_ENROLLMENT, 'POST', $request_with_user_body[1], $access_token_string );
			return $request_with_user_res;
		}
	}

	/**
	 * This function, in case of enrollment action is enrollment_allowed, will check the status code.
	 * In case of 404, it will try to do the request with the user email to get the Open edX supported versions
	 * for the enrollment allowed, if not, it will return the response.
	 *
	 * @param string $response The response array.
	 * @param string $enrollment_data The enrollment data.
	 * @param string $enrollment_action The enrollment action.
	 * @param string $access_token_string The access token.
	 * @return array The response array.
	 */
	public function enrollment_allowed_handler( $response, $enrollment_data, $enrollment_action, $access_token_string ) {

		$http_404_not_found = '404';
		$status_code        = '';

		if ( 'error' === $response[0] ) {
			$status_code = strval( $response[2] );
		}

		if ( $http_404_not_found === $status_code ) {
			return $this->response_or_request_with_username( $response, $enrollment_data, $enrollment_action, $access_token_string );
		} else {
			return $response;
		}
	}

	/**
	 * This function process the request with user_email using the new endpoint.
	 *
	 * @param string $enrollment_data The enrollment data.
	 * @param string $enrollment_action The enrollment action.
	 * @param string $access_token_string The access token.
	 * @param string $request_with_email_body The request body.
	 * @param string $api_endpoint The API endpoint.
	 * @param string $method The HTTP method to use.
	 * @return array The response array.
	 */
	public function process_enrollment_action( $enrollment_data, $enrollment_action, $access_token_string, $request_with_email_body, $api_endpoint, $method ) {

		$request_with_email_res = $this->enrollment_request_api_call( $api_endpoint, $method, $request_with_email_body[1], $access_token_string );
		return $this->request_response_handler( $request_with_email_res, $enrollment_data, $enrollment_action, $access_token_string );
	}

	/**
	 * This function returns the request body depending in the action, if it's enrollment or unenrollment and if it's using email or username.
	 *
	 * @param string $enrollment_data The enrollment data.
	 * @param string $use_old_endpoint If the request is using the old endpoint.
	 * @param string $access_token_string The access token.
	 * @param string $enrollment_action The enrollment action.
	 * @return array The request body.
	 */
	public function get_enrollment_process_body( $enrollment_data, $use_old_endpoint, $access_token_string, $enrollment_action ) {

		$user_or_email       = '';
		$user_or_email_value = '';
		$course_id           = $enrollment_data['enrollment_course_id'];
		$course_mode         = $enrollment_data['enrollment_mode'];
		$request_type        = $enrollment_data['enrollment_request_type'];

		if ( $use_old_endpoint ) {
			$user_or_email       = 'user';
			$user_or_email_value = $this->check_if_user_exists( $enrollment_data['enrollment_email'], $access_token_string );

			if ( 'error' === $user_or_email_value[0] ) {
				return $user_or_email_value;
			} else {
				$user_or_email_value = $user_or_email_value[1];
			}
		} else {
			$user_or_email       = 'email';
			$user_or_email_value = $enrollment_data['enrollment_email'];
		}

		$body = array(
			$user_or_email          => $user_or_email_value,
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

		if ( 'unenroll' === $request_type ) {
			$body['is_active'] = false;
		}

		if ( 'enrollment_force' === $enrollment_action ) {
			$body['force_enrollment'] = true;
		}

		return array( 'success', $body );
	}

	/**
	 * This function returns the enrollment allowed request body depending in the action, if it's enrollment or unenrollment.
	 *
	 * @param string $enrollment_data The enrollment data.
	 * @param string $access_token_string The access token.
	 * @param string $enrollment_action The enrollment action.
	 * @return array The request body.
	 */
	public function get_enrollment_allowed_body( $enrollment_data, $access_token_string, $enrollment_action ) {

		$course_id        = $enrollment_data['enrollment_course_id'];
		$enrollment_email = $enrollment_data['enrollment_email'];
		$request_type     = $enrollment_data['enrollment_request_type'];
		$user_exist       = $this->check_if_user_exists( $enrollment_email, $access_token_string );

		if ( 'success' === $user_exist[0] ) {
			return array( 'user_exists', $user_exist[1] );
		}

		$body = array(
			'email'       => $enrollment_email,
			'course_id'   => $course_id,
			'auto_enroll' => true,
		);

		if ( 'enrollment_allowed_force' === $enrollment_action ) {
			$body['force_enrollment'] = true;
		}

		if ( 'unenroll' === $request_type ) {
			unset( $body['auto_enroll'] );
		}

		return array( 'success', $body );
	}

	/**
	 * Performs a request to the Open edX API endpoint
	 *
	 * @param string $api_endpoint The API endpoint.
	 * @param string $method The HTTP method to use.
	 * @param array  $body The request body.
	 * @param string $access_token_string The access token.
	 *
	 * @return array The response array.
	 */
	public function enrollment_request_api_call( $api_endpoint, $method, $body, $access_token_string ) {

		$domain = get_option( 'openedx-domain' );

		try {

			$response = $this->client->request(
				$method,
				$domain . $api_endpoint,
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
	 * This function checks if the user exists in the Open edX platform with enrollment allowed.
	 * It will return true if the user exists, and false if not.
	 *
	 * @param string $get_enrollments_allowed The response of the enrollment allowed request.
	 * @param string $course_id The course id.
	 * @return boolean If the user exists or not.
	 */
	public function check_user_enrollment_allowed_exists( $get_enrollments_allowed, $course_id ) {

		$enrollments_allowed_data = json_decode( $get_enrollments_allowed[1], true );
		$course_exists            = false;

		foreach ( $enrollments_allowed_data as $enrollment_allowed ) {
			if ( isset( $enrollment_allowed['course_id'] ) && $course_id === $enrollment_allowed['course_id'] ) {
				$course_exists = true;
				break;
			}
		}

		return $course_exists;
	}


	/**
	 * This function get, for a specific user, the courses where the user is enrolled with enrollment allowed.
	 *
	 * @param string $enrollment_email The user email.
	 * @param string $access_token_string The access token.
	 * @return array The response array.
	 */
	public function get_user_enrollments_allowed( $enrollment_email, $access_token_string ) {

		$domain = get_option( 'openedx-domain' );

		try {

			$response = $this->client->request(
				'GET',
				$domain . self::API_ENROLLMENT_ALLOWED,
				array(
					'headers' => array(
						'Authorization' => 'JWT ' . $access_token_string,
						'Content-Type'  => 'application/json',
					),
					'query'   => array(
						'email' => $enrollment_email,
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
