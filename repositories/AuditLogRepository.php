<?php

require_once __DIR__ . '/../database/DatabaseHandler.php';

class AuditLogRepository {
    private $dbHandler;

    public function __construct($dbHandler) {
        $this->dbHandler = $dbHandler;
    }

    public function logTransaction($userId, $action, $details = '') {
        $query = "INSERT INTO audit_logs (user_id, action, details) VALUES (?, ?, ?)";
        $stmt = $this->dbHandler->prepare($query);
        $stmt->bind_param("iss", $userId, $action, $details);
        return $this->dbHandler->execute($stmt);
    }
}

?>