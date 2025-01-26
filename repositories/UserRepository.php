<?php

require_once __DIR__ . '/../database/DatabaseHandler.php';

class UserRepository {
    private $dbHandler;

    public function __construct($dbHandler) {
        $this->dbHandler = $dbHandler;
    }

    public function getUserByUsername($username) {
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->dbHandler->prepare($query);
        $stmt->bind_param("s", $username);
        $this->dbHandler->execute($stmt);
        $result = $this->dbHandler->getResult($stmt);
        return $result->fetch_assoc();
    }
}

?>