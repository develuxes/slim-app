<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class HttpBasicAuthenticationMiddleware {
    protected $db;
    
    public function __construct(){
        $this->db = new db();
    }

    public function authenticate($args) {
        $db = $this->db->connect();

        $password = $args['password'];
        $userId = $args['user'];

        if($password && $userId && $db) {
            $sql = "SELECT *, PASSWORD('".mysqli_real_escape_string($db, $password)."') AS password_hash FROM `users` WHERE `username`='".mysqli_real_escape_string($db, $userId)."'";
            $result = mysqli_query($db, $sql);
            
            if(mysqli_num_rows($result) == 1) {
                return true;
            }
        }
        return false;
    }

    public function handleError($req, $res, $args) {
        $data = [
            "status" => "Unauthorized",
            "message" => $args["message"]
        ];
        
        return $response->withJson($data, 401, JSON_UNESCAPED_SLASHES);
    }


}
