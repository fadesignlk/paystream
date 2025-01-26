<?php
require_once __DIR__ . '/../repositories/ClockingRepository.php';

// classes/Clocking.php
class Clocking {
    private $clockingId;
    private $employeeId;
    private $clockingDate;
    private $clockingTime;
    private $clockingType; // 'In', 'Out', 'Break Start', 'Break End'

    private $clockingRepository;

    public function __construct($dbHandler, $clockingId = null, $employeeId = null, $clockingDate = null, $clockingTime = null, $clockingType = null) {
        $this->clockingRepository = new ClockingRepository($dbHandler);
        $this->clockingId = $clockingId;
        $this->employeeId = $employeeId;
        $this->clockingDate = $clockingDate;
        $this->clockingTime = $clockingTime;
        $this->clockingType = $clockingType;
    }

    public function addClocking($clockingData) {
        return $this->clockingRepository->addClocking($clockingData);
    }

    public function updateClocking($clockingId, $clockingData) {
        return $this->clockingRepository->updateClocking($clockingId, $clockingData);
    }

    public function deleteClocking($clockingId) {
        return $this->clockingRepository->deleteClocking($clockingId);
    }

    public function getClockingById($clockingId) {
        return $this->clockingRepository->getClockingById($clockingId);
    }

    public function getClockingByEmployeeId($employeeId) {
        return $this->clockingRepository->getClockingByEmployeeId($employeeId);
    }

    public function getAllClockings() {
        return $this->clockingRepository->getAllClockings();
    }

    public function getClockingId() { return $this->clockingId; }
    public function getEmployeeId() { return $this->employeeId; }
    public function getClockingTime() { return $this->clockingTime; }
    public function getClockingType() { return $this->clockingType; }
    public function getClockingDate() { return $this->clockingDate; }
    // ... other getters
}


?>