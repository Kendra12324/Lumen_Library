<?php
require_once __DIR__ . '/../config/Database.php';

class User {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    public function login($username, $password) {
        $query = "SELECT * FROM User WHERE Username = ? AND PasswordHash = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0 ? $result->fetch_assoc() : false;
    }

    public function usernameExists($username) {
        $query = "SELECT User_ID FROM User WHERE Username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

   
    public function register($name, $username, $password, $role) {
        $query = "INSERT INTO User (Name, Username, PasswordHash, Role) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssss", $name, $username, $password, $role);
        
        return $stmt->execute();
    }
}
?>
