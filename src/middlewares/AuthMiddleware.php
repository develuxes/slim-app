<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Tuupola\Middleware\HttpBasicAuthentication;

class AuthMiddleware {
    protected $db;

    public function __construct() {
        $this->db = new db();
    }

    public function __invoke(Request $req, Response $res, $next)
    {
        $httpBasicAuthenticator = new HttpBasicAuthenticationMiddleware();
        $httpBasicAuth = new HttpBasicAuthentication([
            "path" => "/v1",
            "secure" => false,
            "realm" => "Protected",
            "authenticator" => [$httpBasicAuthenticator, 'authenticate'],
            "error" => [$httpBasicAuthenticator, 'handleError']
        ]);

        $db = $this->db->connect();
        $apiKey = $req->getHeaderLine('api-key');

        if (!$apiKey) {
            return $res->withStatus(401)->withJson(['error' => 'API Key is missing']);
        }

        if($apiKey && $db) {
            $sql = "SELECT api_key FROM api_keys";
            $apiKeyDatas = $this->db->query($sql)->fetchAll(PDO::FETCH_OBJ);

            $apiKeys = array_map(function($item) {
                return $item->api_key ?? null;
            }, $apiKeyDatas);

            if (in_array($apiKey, $apiKeys)) {
                // API key is valid, proceed to the next middleware or route handler
                return $next($req, $res);
            } else {
                // Invalid API key, handle with HTTP Basic Authentication error
                $errorResponse = $res->withStatus(401)->withJson(['error' => 'Invalid API Key']);
                return $httpBasicAuth->__invoke($req, $errorResponse, $next);
            }
        }

        // If no API key provided or the database connection fails
        $errorResponse = $res->withStatus(401)->withJson(['error' => 'Authentication failed']);
        return $httpBasicAuth->__invoke($req, $errorResponse, $next);
    }
}