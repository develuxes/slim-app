<?php

use Slim\Middleware\HttpBasicAuthentication;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


// Create the Middleware instants
$authMiddleware = new AuthMiddleware();

// Add the API Key middleware to the Slim application
$app->add($authMiddleware);    
