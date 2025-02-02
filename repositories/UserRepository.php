<?php

require_once __DIR__ . '/../database/DatabaseHandler.php';
require_once __DIR__ . '/../utils/Logger.php';

class UserRepository {
    private $dbHandler;
    private $logger;

    public function __construct($dbHandler) {
        $this->dbHandler = $dbHandler;
        $this->logger = new Logger(__DIR__ . '/../logs/user_repository.log');
    }

    public function getUserByUsername($username) {
        $query = "SELECT user_id, username, first_name, last_name, email, created_at, updated_at, updated_by FROM users WHERE username = ?";
        $stmt = $this->dbHandler->prepare($query);
        $stmt->bind_param("s", $username);
        $this->dbHandler->execute($stmt);
        $result = $this->dbHandler->getResult($stmt);
        return $result->fetch_assoc();
    }

    public function findById($userId) {
        $stmt = $this->dbHandler->prepare("SELECT user_id, username, first_name, last_name, email, created_at, updated_at, updated_by FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row) {
            $this->logger->log("User found: " . json_encode($row));
            return $row;
        } else {
            $this->logger->log("No user found with ID: $userId");
            return null;
        }
    }
}

?>