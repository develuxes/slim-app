<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;

require '../vendor/autoload.php';
$app = new \Slim\App;

require '../src/config/db.php';
require __DIR__ . '/../src/authMiddleware/ApiKeyAuthenticator.php';
require __DIR__ . '/../src/authMiddleware/HttpBasicAuthenticator.php';

$app->get('/test', function (Request $req, Response $res, array $args) {      
   return $res->getBody()->write("test success!");
});

$app->run();
