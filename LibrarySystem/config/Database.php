<?php
class Database {

    private $host = "127.0.0.1"; 
    private $user = "root";
    private $pass = ""; 
    private $dbname = "library";
    private $port = 3308; 

    public $conn;

    public function __construct() {
        $this->connect();
    }

    private function connect() {

        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname, $this->port);

        if ($this->conn->connect_error) {
            die(" Database Connection failed: " . $this->conn->connect_error . 
                "<br> Tip: check your database name, port, and MySQL password in XAMPP/phpMyAdmin.");
        }
    }
}
?>
