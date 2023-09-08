<?php
/**
 * Openedx plugin settings page.
 *
 * @category   Views
 * @package    WordPress
 * @subpackage Openedx_Woocommerce_Plugin
 * @since      1.6.0
 */

namespace App\admin\views;

use App\model\Openedx_Woocommerce_Plugin_Api_Calls;
use DateTime;
use DateInterval;

/**
 * This class allows the user to configure the plugin settings
 * focusing on the connection between Open edX platform and the store.
 */
class Openedx_Woocommerce_Plugin_Settings {
	/**
	 * API call variable.
	 *
	 * @var Openedx_Woocommerce_Plugin_Api_Calls
	 */
	private $api_call;

	/**
	 * Class constructor.
	 *
	 * Initializes the API Call class.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->api_call = new Openedx_Woocommerce_Plugin_Api_Calls();
	}

	/**
	 * Add the plugin settings submenu page.
	 *
	 * Registers a new administration submenu page under the Settings menu
	 * for the Open edX plugin settings.
	 *
	 * @return void
	 */
	public function openedx_settings_submenu() {
		add_submenu_page(
			'options-general.php',
			'Open edX Sync Plugin Settings',
			'Open edX Sync Plugin',
			'manage_options',
			'openedx-settings',
			array( $this, 'openedx_settings_page' )
		);
	}

	/**
	 * Output the plugin settings page.
	 *
	 * Renders the form and fields for the Open edX plugin settings page.
	 *
	 * @return void
	 */
	public function openedx_settings_page() {
		?>
		<div class="wrap">
			<h2>Open edX Sync Plugin Settings</h2>
			<form method="post" action="options.php">
				<?php settings_fields( 'openedx-settings-group' ); ?>
				<?php do_settings_sections( 'openedx-settings' ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register settings and fields.
	 *
	 * Uses the Settings API to register the settings section, fields, and options
	 * for the Open edX plugin configuration.
	 *
	 * @return void
	 */
	public function openedx_settings_init() {
		add_settings_section(
			'openedx-settings-section',
			'',
			array( $this, 'openedx_settings_section_callback' ),
			'openedx-settings'
		);

		add_settings_field(
			'openedx-domain',
			'Open edX Domain',
			array( $this, 'openedx_domain_callback' ),
			'openedx-settings',
			'openedx-settings-section'
		);

		add_settings_field(
			'openedx-client-id',
			'Client id',
			array( $this, 'openedx_client_id_callback' ),
			'openedx-settings',
			'openedx-settings-section'
		);

		add_settings_field(
			'openedx-client-secret',
			'Client secret',
			array( $this, 'openedx_client_secret_callback' ),
			'openedx-settings',
			'openedx-settings-section'
		);

		add_settings_field(
			'openedx-jwt-token',
			'JWT Token',
			array( $this, 'openedx_jwt_token_callback' ),
			'openedx-settings',
			'openedx-settings-section'
		);

		register_setting(
			'openedx-settings-group',
			'openedx-domain',
			'sanitize_url'
		);

		register_setting(
			'openedx-settings-group',
			'openedx-client-id',
			'sanitize_text_field'
		);

		register_setting(
			'openedx-settings-group',
			'openedx-client-secret',
			'sanitize_text_field'
		);

		register_setting(
			'openedx-settings-group',
			'openedx-jwt-token',
			'sanitize_text_field'
		);

		if ( isset( $_POST['generate_new_token'] ) ) {
			$this->set_new_token();
		}

		if ( get_transient( 'openedx_success_message' ) ) {
			add_settings_error( 'success_message', 'success_message', 'New token generated', 'updated' );
			delete_transient( 'openedx_success_message' );
		}
	}

	/**
	 * Make API call to generate new JWT token.
	 *
	 * Calls the Openedx_Woocommerce_Plugin_Api_Calls class to generate
	 * a new JWT token using the Open edX API credentials saved in options.
	 *
	 * Handles errors from the API request and redirects back to the settings
	 * page with success or error messages.
	 *
	 * @return void
	 */
	public function set_new_token() {

		$response         = $this->api_call->generate_token(
			get_option( 'openedx-client-id' ),
			get_option( 'openedx-client-secret' ),
			get_option( 'openedx-domain' )
		);
		$response_message = $response[0];

		if ( 'success' === $response_message ) {

			$response_data = $response[1];

			$exp_time = $response_data['expires_in'];
			$exp_date = new DateTime();
			$exp_date->add( new DateInterval( 'PT' . $exp_time . 'S' ) );
			update_option( 'openedx-token-expiration', $exp_date );

			$nonce = wp_create_nonce( 'token_generated_nonce' );
			update_option( 'openedx-jwt-token', $response_data['access_token'] );

			set_transient( 'openedx_success_message', 'Token generated', 10 );
			wp_safe_redirect( admin_url( 'options-general.php?page=openedx-settings' ) );
			exit();
		} else {

			settings_errors( 'openedx-settings', 'true' );
			if ( 'error_has_response' === $response_message ) {
				add_settings_error(
					'token_error',
					'token_error',
					'Error: ' . $response[1] . ' - ' . json_decode( $response[2], true )['error']
				);
			} else {
				add_settings_error(
					'token_error',
					'token_error',
					'Error: ' . $response[1]->getMessage()
				);
			}
		}
	}

	/**
	 * Output the domain settings field.
	 *
	 * Retrieves the saved domain value and outputs an input field and description text.
	 *
	 * @return void
	 */
	public function openedx_domain_callback() {
		$value = get_option( 'openedx-domain' );
		?>

		<input class="setting_input" type="text" name="openedx-domain" id="openedx-domain" 
			value="<?php echo esc_attr( $value ); ?>" required />

		<p class='description'>Your Open edX platform's web address.</p>

		<?php
	}

	/**
	 * Output the client ID settings field.
	 *
	 * Retrieves the saved client ID value and outputs an input field and description text.
	 *
	 * @return void
	 */
	public function openedx_client_id_callback() {
		$value = get_option( 'openedx-client-id' );
		?>

		<input class="setting_input" type="text" name="openedx-client-id" id="openedx-client-id" 
			value="<?php echo esc_attr( $value ); ?>" required />

		<p class="description">Identifier for OAuth application in your Open edX 
			platform.</p>

		<?php
	}

	/**
	 * Output the client secret settings field.
	 *
	 * Retrieves the saved client secret value and outputs a password input field and description text.
	 *
	 * @return void
	 */
	public function openedx_client_secret_callback() {

		$value = get_option( 'openedx-client-secret' );
		?>

		<input class="setting_input" type="text" name="openedx-client-secret" id="openedx-client-secret" 
			value="<?php echo esc_attr( $value ); ?>" required />

		<p class="description">
			Confidential key for OAuth application in your Open edX platform.
		</p>

		<?php
	}

	/**
	 * Output the JWT token settings field.
	 *
	 * Retrieves the saved JWT token value and outputs a text input field, generate token button,
	 * and description text.
	 *
	 * @return void
	 */
	public function openedx_jwt_token_callback() {
		$value = get_option( 'openedx-jwt-token' );
		if ( ! empty( $value ) ) {
			$first_part   = substr( $value, 0, 4 );
			$last_part    = substr( $value, -4 );
			$hidden_part  = str_repeat( '*', 8 );
			$masked_value = $first_part . $hidden_part . $last_part;
		} else {
			$masked_value = '';
		}

		?>

		<div class="openedx-jwt-token-wrapper">

			<input class="setting_input" class="openedx-jwt-token-input" type="text" name="openedx-jwt-token" id="openedx-jwt-token"
				value="<?php echo esc_attr( $value ); ?>" hidden/>
			<input class="setting_input" class="openedx-jwt-token-input" type="text" value="<?php echo esc_attr( $masked_value ); ?>" readonly/>

			<form method="post">
				<button class="button" type="submit" name="generate_new_token" id="generate-jwt-token">Generate JWT Token</button>
			</form>
			

		</div>

		<p class="description"> Select the Generate Token button to obtain a JWT Token. </p>

		<?php
	}

	/**
	 * Output introductory text for the settings section.
	 *
	 * Echoes text prompting the user to fill in and save the settings.
	 *
	 * @return void
	 */
	public function openedx_settings_section_callback() {
		printf(
			'Configuring the necessary parameters here to establish the connection between this plugin and your Open edX platform.'
		);
	}
}

