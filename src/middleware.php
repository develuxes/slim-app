<?php

use Slim\Middleware\HttpBasicAuthentication;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


// Create the Middleware instants
$apiKeyAuthenticator = new ApiKeyAuthenticationMiddleware();
$httpBasicAuthenticator = new HttpBasicAuthenticationMiddleware();

// Add the API Key middleware to the Slim application
$app->add($apiKeyAuthenticator);    


// Add the HTTP Basic Authentication middleware only if API Key fails
$app->add(new HttpBasicAuthentication([
    "path" => "/v1",
    "secure" => false,
    "realm" => "Protected",
    "authenticator" => [$httpBasicAuthenticator, 'authenticate'],
    "error" => [$httpBasicAuthenticator, 'handleError']
]));