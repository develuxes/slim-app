<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Middleware\HttpBasicAuthentication;


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
                   
            if (in_array($apiKey, $apiKeys)) return $next($req, $res);
            return $httpBasicAuth;
        }
        
        return $httpBasicAuth;
    }
}
