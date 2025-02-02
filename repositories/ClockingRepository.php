<?php
require_once __DIR__ . '/../database/DatabaseHandler.php';
require_once __DIR__ . '/../utils/Logger.php';

class ClockingRepository {
    private $dbHandler;
    private $logger;

    public function __construct($dbHandler) {
        $this->dbHandler = $dbHandler;
        $this->logger = new Logger(__DIR__ . '/../logs/clocking_repository.log');
    }

    public function addClocking($clockingData) {
        $query = "INSERT INTO clockings (employee_id, clocking_date, clocking_time, clocking_type, created_at, updated_at, updated_by) VALUES (?, ?, ?, ?, NOW(), NOW(), ?)";
        $stmt = $this->dbHandler->prepare($query);
        $stmt->bind_param("isssi", $clockingData['employee_id'], $clockingData['clocking_date'], $clockingData['clocking_time'], $clockingData['clocking_type'], $clockingData['updated_by']);
        return $this->dbHandler->execute($stmt);
    }

    public function updateClocking($clockingId, $clockingData) {
        $query = "UPDATE clockings SET employee_id = ?, clocking_date = ?, clocking_time = ?, clocking_type = ?, updated_at = NOW(), updated_by = ? WHERE clocking_id = ?";
        $stmt = $this->dbHandler->prepare($query);
        $stmt->bind_param("isssii", $clockingData['employee_id'], $clockingData['clocking_date'], $clockingData['clocking_time'], $clockingData['clocking_type'], $clockingData['updated_by'], $clockingId);
        return $this->dbHandler->execute($stmt);
    }

    public function deleteClocking($clockingId) {
        $query = "DELETE FROM clockings WHERE clocking_id = ?";
        $stmt = $this->dbHandler->prepare($query);
        $stmt->bind_param("i", $clockingId);
        return $this->dbHandler->execute($stmt);
    }

    public function getClockingById($clockingId) {
        $query = "SELECT * FROM clockings WHERE clocking_id = ?";
        $stmt = $this->dbHandler->prepare($query);
        $stmt->bind_param("i", $clockingId);
        $this->dbHandler->execute($stmt);
        $result = $this->dbHandler->getResult($stmt);
        return $result->fetch_assoc();
    }

    public function getClockingByEmployeeId($employeeId) {
        $query = "SELECT * FROM clockings WHERE employee_id = ?";
        $stmt = $this->dbHandler->prepare($query);
        $stmt->bind_param("i", $employeeId);
        $this->dbHandler->execute($stmt);
        $result = $this->dbHandler->getResult($stmt);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllClockings() {
        $query = "SELECT * FROM clockings order by clocking_date desc, clocking_time desc";
        $stmt = $this->dbHandler->prepare($query);
        $this->dbHandler->execute($stmt);
        $result = $this->dbHandler->getResult($stmt);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function findByEmployeeIdAndPeriod($employeeId, $payPeriodStart, $payPeriodEnd) {
        $this->logger->log("Finding clockings for employee ID: $employeeId, Pay Period: $payPeriodStart to $payPeriodEnd");
        $stmt = $this->dbHandler->prepare("SELECT * FROM clockings WHERE employee_id = ? AND clocking_date BETWEEN ? AND ?");
        $stmt->bind_param("iss", $employeeId, $payPeriodStart, $payPeriodEnd);
        $stmt->execute();
        $result = $stmt->get_result();
        $clockings = [];
        while ($row = $result->fetch_assoc()) {
            $clockings[] = $row;
        }
        $this->logger->log("Clockings found: " . json_encode($clockings));
        return $clockings;
    }
}
?>