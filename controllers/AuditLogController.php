<?php
require_once __DIR__ . '/../models/AuditLog.php';

class AuditLogController {
    private $auditLogModel;

    public function __construct($dbHandler) {
        $this->auditLogModel = new AuditLog($dbHandler);
    }

    public function logTransaction($userId, $action, $description) {
        return $this->auditLogModel->logTransaction($userId, $action, $description);
    }
}
?>