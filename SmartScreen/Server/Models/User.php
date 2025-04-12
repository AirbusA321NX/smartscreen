<?php
class User {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function create($username, $password) {
        $stmt = $this->db->conn->prepare(
            "INSERT INTO users (username, password) VALUES (?, ?)"
        );
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        return $stmt->execute([$username, $hashedPassword]);
    }

    public function findByUsername($username) {
        $stmt = $this->db->conn->prepare(
            "SELECT * FROM users WHERE username = ?"
        );
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
}
?>