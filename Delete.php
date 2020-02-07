<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Delete {
    protected $authServerUrl;
    protected $realm;
    private $user_manager;
    private $credential;
    private $username;
    private $token;
    protected $client;

    public function __construct(string $authServerUrl, string $realm, string $owner, string $credential, string $username)
    {
        $this->authServerUrl = $authServerUrl;
        $this->realm = $realm;
        $this->user_manager = $owner;
        $this->credential = $credential;
        $this->username = $username;
        $this->client = new Client();
        $this->token = "";
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
        return $token = json_decode($response_body)->access_token;
    }

    // getUserId function return ID of a user based on their username which is
    // given as query parameter in GET request
    private function getUserId() {
        try {
            $this->token = $this->getToken();
            $response = $this->client->get($this->getUserEndpoint(), [
                'query' => 'username='.$this->username,
                'headers' => [
                    'Authorization' => 'Bearer '.$this->token
                ]
            ]);

            $response_body = $response->getBody();
            if($response_body != "[]") {
                $userId = json_decode($response_body)[0]->id;
                return array($userId, null);
            } else {
                exit('Response status code: 404'.PHP_EOL.'Error Message: User not found');
            }
            
        } catch(RequestException $e) {
            return array(null, $e);
        }
    }

    // deleteUser function deletes the user based on the given user ID
    public function deleteUser() {
        $userIdResponse = $this->getUserId();
        if(isset($userIdResponse[0])) {
            try{
                $response = $this->client->delete($this->getUserEndpoint().'/'.$userIdResponse[0], [
                    'headers' => [
                        'Authorization' => 'Bearer '.$this->token
                    ]
                ]);

                return array($response, null);

            } catch(RequestException $e) {
                return array(null, $e);
            }
        } else {
            return array(null, $userIdResponse[1]);
        }
    }
}


$client = new Delete('keycloak_auth_base_url', 'real_name', 'user_with_user_management_role', 'password_of_user', 'username_to_update');
$response = $client->deleteUser();
if (isset($response[0])) {
    echo 'User deleted successfully with status code: '.$response[0]->getStatusCode();
} elseif($response[1]->hasResponse()) {
    $exception = $response[1]->getResponse();
    echo 'User deletion failed. Response status code: '.(string) $exception->getStatusCode().PHP_EOL.'Error message: '.$exception->getReasonPhrase();
} else {
    echo 'User deletion failed. Response status code: 503'.PHP_EOL.'Error Message: Failed to connect to the server';
}
