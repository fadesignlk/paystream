<?php
require_once __DIR__ . '/../repositories/AuditLogRepository.php';

class AuditLog {
    private $auditLogRepository;

    public function __construct($dbHandler) {
        $this->auditLogRepository = new AuditLogRepository($dbHandler);
    }

    public function logTransaction($userId, $action, $description) {
        return $this->auditLogRepository->logTransaction($userId, $action, $description);
    }
}

?>