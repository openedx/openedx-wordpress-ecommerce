<?php

namespace App\model;

require_once(plugin_dir_path(dirname(__DIR__)).'vendor/autoload.php');
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\GuzzleException;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Openedx_Woocommerce_Plugin_Api_Calls {

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
    public function generate_token($client_id, $client_secret, $domain){

        try {
            $response = $this->client->request('POST', $domain.'/oauth2/access_token', [
                'form_params' => [
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'grant_type' => 'client_credentials',
                    'token_type' => 'jwt'
                ]
            ]);
        
            $status_code = $response->getStatusCode();
            $response_data = json_decode($response->getBody(), true);
            return array("success", $response_data);
            
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $status_code = $e->getResponse()->getStatusCode();
                $error_data = $e->getResponse()->getBody()->getContents();
                return array("error_has_response", $status_code, $error_data);
            } else {
                return array("error_no_response", $e);
            }
        } catch (GuzzleException $e) {
            return array("error_no_response", $e);
        }
    }

}
