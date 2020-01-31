# Create User for Keycloak using PHP

This Project aims at adding a new user in keycloak using PHP Guzzle package through sending POST request to Admin REST API.

## Prerequisites

The following prerequisites are utilized in this project:

- OS: Windows
- Keycloak
- PHP >= 7.2
- Composer
- Guzzle package ~6.0

## Getting Started

### Run Keycloak

First of all you need to run keycloak server. This project assumes the Keycloak server is running on http://localhost:8180

To run Keycloak server on port 8180 using git bash you can run following command in `bin` directory of Keycloak:

```
$ ./standalone.sh -Djboss.socket.binding.port-offset=100
```

### Installing Guzzle

To install Guzzle through Composer:

```
composer require guzzlehttp/guzzle
```

### Run The Project

You can run the project through one of the below options:

1. If PHP is installed on your system, you can run the project using following command and see the output in console. Note that you should run this command where the Create.php is located.

```
$ php Create.php
```

2. Using XAMPP (or WAMPP): You can see the output of the project by opening `Create.php` file in your browser after you run Apache server in XAMPP (WAMPP) Control Panel.

## Usage in Other Files

If you want to use `Create` class in other php files, you can use the following code snippet.

```php
$token = new Create('keycloak_server_url', 'real_name', 'user_with_user_management_role', 'password_of_user');
$response = $token->createUser();
if (isset($response[0])) {
    echo 'Response Status Code: '.$response[0]->getStatusCode();
} elseif($response[1]->hasResponse()) {
    $exception = $response[1]->getResponse();
    echo 'User creation failed. Response status code: '.(string) $exception->getStatusCode().PHP_EOL.'Error message: '.$exception->getReasonPhrase();
} else {
    echo 'User creation failed. Response status code: 503'.PHP_EOL.'Error Message: Failed to connect to the server';
}
```

## Author

Mohammad Mojrian - User Creation in Keycloak using PHP
