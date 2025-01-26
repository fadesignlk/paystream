<?php
require_once __DIR__ . '/../models/Clocking.php';

class ClockingController {
    private $clockingModel;

    public function __construct($dbHandler) {
        $this->clockingModel = new Clocking($dbHandler);
    }

    public function addClocking($clockingData) {
        return $this->clockingModel->addClocking($clockingData);
    }

    public function updateClocking($clockingId, $clockingData) {
        return $this->clockingModel->updateClocking($clockingId, $clockingData);
    }

    public function deleteClocking($clockingId) {
        return $this->clockingModel->deleteClocking($clockingId);
    }

    public function getClockingById($clockingId) {
        return $this->clockingModel->getClockingById($clockingId);
    }

    public function getClockingByEmployeeId($employeeId) {
        return $this->clockingModel->getClockingByEmployeeId($employeeId);
    }

    public function getAllClockings() {
        return $this->clockingModel->getAllClockings();
    }
}
?>