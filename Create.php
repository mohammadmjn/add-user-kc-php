<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Create {

    protected $authServerUrl;
    protected $realm;
    private $user_manager;
    private $credential;
    protected $client;

    public function __construct(string $authServerUrl, string $realm, string $owner, string $credential)
    {
        $this->authServerUrl = $authServerUrl;
        $this->realm = $realm;
        $this->user_manager = $owner;
        $this->credential = $credential;
        $this->client = new Client();
    }

    // getTokenEndpoint function generates "token" endpoint of the server
    private function getTokenEndpoint() {
        return "$this->authServerUrl/realms/$this->realm/protocol/openid-connect/token";
    }

    // getUserEndpoint function generates "users" endpoint of the server
    private function getUserEndpoint() {
        return "$this->authServerUrl/admin/realms/$this->realm/users";
    }

    // getToken function gets token of user manager of the specified scope of SSO
    // using POST request of GuzzleHttp package
    private function getToken() {
        $response = $this->client->post($this->getTokenEndpoint(), [
            'form_params' => [
                'username' => $this->user_manager,
                'password' => $this->credential,
                'grant_type' => 'password',
                'client_id' => 'admin-cli'
            ]
        ]);
        
        $response_body = (string) $response->getBody();
        $accessToken = json_decode($response_body)->access_token;
        
        return $accessToken;
    }

    // createUser function creates the user in the desired scope
    // using POST request of GuzzleHttp package
    public function createUser() {
        try{
            $response = $this->client->post($this->getUserEndpoint(), [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer '.$this->getToken()
                ],
                'json' => [
                    "username"      => "desired_username",
                    "enabled"       => true,
                    "totp"          => false,
                    "emailVerified" => false,
                    "firstName"     => "first_name",
                    "lastName"      => "last_name",
                    "email"         => "email",
                    "attributes"    => [
                        "mobile" => "xxxxxxxxxxx",
                    ],
                    "credentials"   => array([
                        "type"  => "password",
                        "value" => "desired_password"
                    ]),
                    "requiredActions" => array("VERIFY_EMAIL", "UPDATE_PASSWORD")
                ]
            ]);
        } catch(RequestException $e) {
            return array(null, $e);
        }

        return array($response, null);
    }
}

// Here CreateUser function is called to add a new user to SSO server in the specified scope (e.g. demo)
$token = new Create('http://localhost:8180/auth', 'real_name', 'user_with_user_management_role', 'password_of_user');
$response = $token->createUser();
if (isset($response[0])) {
    echo 'Response Status Code: '.$response[0]->getStatusCode();
} elseif($response[1]->hasResponse()) {
    $exception = $response[1]->getResponse();
    echo 'User creation failed. Response status code: '.(string) $exception->getStatusCode().PHP_EOL.'Error message: '.$exception->getReasonPhrase();
} else {
    echo 'User creation failed. Response status code: 503'.PHP_EOL.'Error Message: Failed to connect to the server';
}
