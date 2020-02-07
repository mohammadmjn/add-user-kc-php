# CRUD for Users of Keycloak using PHP

This Project aims at presenting a demo of enabling CRUD on users of a realm in keycloak using PHP Guzzle package through sending HTTP requests to Admin REST API. Based on the desired task, it may be needed to send 2 or 3 HTTP requests to different endpoints.

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

## Run The Project

You can run each of `Create`, `Update` or `Delete` classes of the project through one of the below options:

1. If PHP is installed on your system, you can run the intended class using following command and see the output in console. Note that you should run this command where the file (`Create.php`, `Update.php` or `Delete.php`) is located. For example, to run `Create` class, you can run:

```
$ php Create.php
```

2. Using XAMPP (or WAMPP): You can see the output of the project by opening each file (e.g. `Delete.php`) in your browser after you run Apache server in XAMPP (WAMPP) Control Panel.

## Usage in Other Files

### Create class

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

### Update class

In order to use the `Update` class in another PHP file, you can use following code snippet:

```php
$update_client = new Update('keycloak_auth_base_url', 'real_name', 'user_with_user_management_role', 'password_of_user', 'username_to_update');
$response = $update_client->updateUser();
if (isset($response[0])) {
    echo 'User updated successfully with status code: '.$response[0]->getStatusCode();
} elseif($response[1]->hasResponse()) {
    $exception = $response[1]->getResponse();
    echo 'User Update failed. Response status code: '.(string) $exception->getStatusCode().PHP_EOL.'Error message: '.$exception->getReasonPhrase();
} else {
    echo 'User Update failed. Response status code: 503'.PHP_EOL.'Error Message: Failed to connect to the server';
}
```

### Delete class

Last but not least, the `Delete` class can be used by applying following example code in other php files:

```php
$client = new Delete('keycloak_auth_base_url', 'real_name', 'user_with_user_management_role', 'password_of_user', 'username_to_delete');
$response = $client->deleteUser();
if (isset($response[0])) {
    echo 'User deleted successfully with status code: '.$response[0]->getStatusCode();
} elseif($response[1]->hasResponse()) {
    $exception = $response[1]->getResponse();
    echo 'User deletion failed. Response status code: '.(string) $exception->getStatusCode().PHP_EOL.'Error message: '.$exception->getReasonPhrase();
} else {
    echo 'User deletion failed. Response status code: 503'.PHP_EOL.'Error Message: Failed to connect to the server';
}
```

## Author

Mohammad Mojrian - User Creation in Keycloak using PHP
