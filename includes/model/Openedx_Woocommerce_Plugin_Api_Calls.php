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
        
            $statusCode = $response->getStatusCode();
            $responseData = json_decode($response->getBody(), true);
        
            if (isset($responseData['access_token'])) {
                $accessToken = $responseData['access_token'];
                return $accessToken;
            }    
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $errorData = $e->getResponse()->getBody()->getContents();
                return array($statusCode, $errorData);
            } else {
                return $e;
            }
        } catch (GuzzleException $e) {
            return $e;
        }
    }

}
