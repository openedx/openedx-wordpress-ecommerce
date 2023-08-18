<?php

namespace App\admin\views;

class Openedx_Woocommerce_Plugin_Settings
{

    /**
     * Add the plugin settings submenu page.
     *
     * Registers a new administration submenu page under the Settings menu
     * for the Open edX plugin settings.
     *
     * @return void
     */
    public function openedx_settings_submenu() 
    {
        add_submenu_page(
            'options-general.php',
            'Open edX Settings',
            'Open edX Sync',
            'manage_options',
            'openedx-settings',
            array($this, 'openedx_settings_page')
        );
    }

    /**
     * Output the plugin settings page.
     *
     * Renders the form and fields for the Open edX plugin settings page.
     *
     * @return void
     */
    public function openedx_settings_page() 
    {
        ?>
        <div class="wrap">
            <h2>Open edX Settings</h2>
            <form method="post" action="options.php">
                <?php settings_fields('openedx-settings-group'); ?>
                <?php do_settings_sections('openedx-settings'); ?>
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
    function openedx_settings_init() 
    {
        add_settings_section(
            'openedx-settings-section',
            '',
            array($this, 'openedx_settings_section_callback'),
            'openedx-settings'
        );
    
        add_settings_field(
            'openedx-domain',
            'Open edX Domain',
            array($this, 'openedx_domain_callback'),
            'openedx-settings',
            'openedx-settings-section'
        );
    
        add_settings_field(
            'openedx-client-id',
            'Client id',
            array($this, 'openedx_client_id_callback'),
            'openedx-settings',
            'openedx-settings-section'
        );
    
        add_settings_field(
            'openedx-client-secret',
            'Client secret',
            array($this, 'openedx_client_secret_callback'),
            'openedx-settings',
            'openedx-settings-section'
        );
    
        add_settings_field(
            'openedx-jwt-token',
            'JWT Token',
            array($this, 'openedx_jwt_token_callback'),
            'openedx-settings',
            'openedx-settings-section'
        );
    
        register_setting(
            'openedx-settings-group',
            'openedx-domain',
            array($this, 'custom_sanitize_alphanumeric')
        );
    
        register_setting(
            'openedx-settings-group',
            'openedx-client-id',
            array($this, 'custom_sanitize_alphanumeric')
        );
    
        register_setting(
            'openedx-settings-group',
            'openedx-client-secret',
            array($this, 'custom_sanitize_alphanumeric')
        );
    
        register_setting(
            'openedx-settings-group',
            'openedx-jwt-token',
            array($this, 'custom_sanitize_alphanumeric')
        );
    }

    /**
     * Output the domain settings field.
     *
     * Retrieves the saved domain value and outputs an input field and description text.
     *
     * @return void
     */
    public function openedx_domain_callback()
    {
        $value = get_option( 'openedx-domain' ); ?>

        <input type="text" name="openedx-domain" id="openedx-domain" 
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
    public function openedx_client_id_callback()
    {
        $value = get_option( 'openedx-client-id' ); ?>

        <input type="text" name="openedx-client-id" id="openedx-client-id" 
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
    public function openedx_client_secret_callback()
    {

        $value = get_option( 'openedx-client-secret' ); ?>

        <input type="text" name="openedx-client-secret" id="openedx-client-secret" 
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
    public function openedx_jwt_token_callback()
    {

        $value = get_option('openedx-jwt-token');
        $masked_value = str_repeat('*', strlen($value) - 4) . substr($value, -4); ?>

        <div class="openedx-jwt-token-wrapper">

            <input class="openedx-jwt-token-input" type="text" name="openedx-jwt-token" id="openedx-jwt-token"
                value="<?php echo esc_attr($masked_value); ?>" />

            <button class="button" type="button" id="generate-jwt-token">Generate JWT Token</button>

        </div>

        <p class="description">
        Leave blank if you're going to generate the token, otherwise enter the JWT 
        token and save.
        </p>

    <?php
    }

    /**
     * Output introductory text for the settings section.
     *
     * Echoes text prompting the user to fill in and save the settings.
     * 
     * @return void
     */
    function openedx_settings_section_callback()
    {
        printf( 'Configuring the necessary parameters here to establish
        the connection between this plugin and your Open edX platform.');
    }

    /**
     * Sanitize input string to only contain alphanumeric characters.
     *
     * This removes any non-alphanumeric characters from the given input string.
     *
     * @param string $input The input string to sanitize.
     * @return string The sanitized input string containing only alphanumeric characters.
     */
    function custom_sanitize_alphanumeric($input) 
    {
        return preg_replace('/[^a-zA-Z0-9]/', '', sanitize_text_field($input));
    }
    
}
