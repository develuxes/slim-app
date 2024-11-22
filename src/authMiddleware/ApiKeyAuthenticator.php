<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class ApiKeyAuthenticationMiddleware {
    protected $db;

    public function __construct() {
        $this->db = new db();
    }

    public function __invoke(Request $req, Response $res, $next)
    {
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
                   
            // If API Key is valid, proceed without invoking the next middleware
            if (in_array($apiKey, $apiKeys)) return $next($req, $res);

        }

        // If API Key authentication fails, proceed to the next middleware
        return $next($req, $res);
    }
}
