<?php
    use Illuminate\Database\Capsule\Manager as Capsule;

    class db{
        // Properties
        private $driver = 'mysql';
        private $host = 'localhost';
        private $user = 'root';
        private $pass = '';
        private $db = 'slimdb';
        private $chartset = 'utf8';
        private $collation = 'utf8_unicode_ci';
        private $prefix = '';


        // Connect
        public function connect(){
            $mysql_connect_str = "mysql:host=$this->host;dbname=$this->db";
            $dbConnection = new PDO($mysql_connect_str, $this->user, $this->pass);
            $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $dbConnection;
        }
    }