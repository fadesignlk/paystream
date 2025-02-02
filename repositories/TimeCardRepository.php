<?php

require_once __DIR__ . '/../utils/Logger.php';

class TimeCardRepository {
    private $dbHandler;
    private $logger;

    public function __construct($dbHandler) {
        $this->dbHandler = $dbHandler;
        $this->logger = new Logger(__DIR__ . '/../logs/timecard.log');
    }

    public function save(TimeCard $timeCard) {
        $stmt = $this->dbHandler->prepare("INSERT INTO time_cards (employee_id, clock_in_date, clock_out_date, clock_in_time, clock_out_time, reported_hours, overtime) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $timeCard->getEmployeeId(), $timeCard->getClockInDate(), $timeCard->getClockOutDate(), $timeCard->getClockInTime(), $timeCard->getClockOutTime(), $timeCard->getReportedHours(), $timeCard->getOvertime());
        if ($stmt->execute()) {
            $this->logger->log("TimeCard saved: " . json_encode($timeCard));
        } else {
            $this->logger->log("Error saving TimeCard: " . $stmt->error);
        }
    }

    public function update(TimeCard $timeCard) {
        $this->logger->log("TimeCard update repo: " . $timeCard->getEmployeeId());
        $stmt = $this->dbHandler->prepare("UPDATE time_cards SET clock_out_date = ?, clock_out_time = ?, reported_hours = ?, overtime = ? WHERE employee_id = ? AND clock_in_date = ?");
        $stmt->bind_param("ssssis", $timeCard->getClockOutDate(), $timeCard->getClockOutTime(), $timeCard->getReportedHours(), $timeCard->getOvertime(), $timeCard->getEmployeeId(), $timeCard->getClockInDate());
        if ($stmt->execute()) {
            $this->logger->log("TimeCard updated: " . json_encode($timeCard));
        } else {
            $this->logger->log("Error updating TimeCard: " . $stmt->error);
        }
    }

    public function findByEmployeeIdAndClockInDate($employeeId, $clockInDate) {
        $stmt = $this->dbHandler->prepare("SELECT * FROM time_cards WHERE employee_id = ? AND clock_in_date = ?");
        $stmt->bind_param("is", $employeeId, $clockInDate);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row) {
            $this->logger->log("TimeCard found: " . json_encode($row));
            return new TimeCard($row['employee_id'], $row['clock_in_date'], $row['clock_in_time'], $row['clock_out_date'], $row['clock_out_time'], $row['reported_hours'], $row['overtime']);
        }
        $this->logger->log("No TimeCard found for employee_id: $employeeId and clock_in_date: $clockInDate");
        return null;
    }

    public function findMostRecentClockIn($employeeId) {
        $this->logger->log("Finding most recent clock-in for employee_id: $employeeId");
        $stmt = $this->dbHandler->prepare("SELECT * FROM time_cards WHERE employee_id = ? AND clock_out_time IS NULL ORDER BY clock_in_date DESC, clock_in_time DESC LIMIT 1");
        $stmt->bind_param("i", $employeeId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row) {
            $this->logger->log("Most recent clock-in found: " . json_encode($row));
            return new TimeCard($row['employee_id'], $row['clock_in_date'], $row['clock_in_time'], $row['clock_out_date'], $row['clock_out_time'], $row['reported_hours'], $row['overtime']);
        } else {
            $this->logger->log("No recent clock-in found for employee_id: $employeeId");
            return null;
        }
    }

    public function findAll() {
        $this->logger->log("Fetching all time cards");
        $stmt = $this->dbHandler->prepare("SELECT * FROM time_cards");
        $stmt->execute();
        $result = $stmt->get_result();
        $timeCards = [];
        while ($row = $result->fetch_assoc()) {
            $timeCards[] = new TimeCard($row['employee_id'], $row['clock_in_date'], $row['clock_in_time'], $row['clock_out_date'], $row['clock_out_time'], $row['reported_hours'], $row['overtime']);
        }
        $this->logger->log("Time cards fetched: " . json_encode($timeCards));
        return $timeCards;
    }
}