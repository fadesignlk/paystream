<?php

class TimeCardRepository {
    private $dbHandler;

    public function __construct($dbHandler) {
        $this->dbHandler = $dbHandler;
    }

    public function save(TimeCard $timeCard) {
        $stmt = $this->dbHandler->prepare("INSERT INTO time_cards (employee_id, clock_in_date, clock_out_date, clock_in_time, clock_out_time, reported_hours, overtime) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $timeCard->getEmployeeId(), $timeCard->getClockInDate(), $timeCard->getClockOutDate(), $timeCard->getClockInTime(), $timeCard->getClockOutTime(), $timeCard->getReportedHours(), $timeCard->getOvertime());
        $stmt->execute();
    }

    public function update(TimeCard $timeCard) {
        $stmt = $this->dbHandler->prepare("UPDATE time_cards SET clock_out_date = ?, clock_out_time = ?, reported_hours = ?, overtime = ? WHERE employee_id = ? AND clock_in_date = ?");
        $stmt->bind_param("sssssi", $timeCard->getClockOutDate(), $timeCard->getClockOutTime(), $timeCard->getReportedHours(), $timeCard->getOvertime(), $timeCard->getEmployeeId(), $timeCard->getClockInDate());
        $stmt->execute();
    }

    public function findByEmployeeIdAndClockInDate($employeeId, $clockInDate) {
        $stmt = $this->dbHandler->prepare("SELECT * FROM time_cards WHERE employee_id = ? AND clock_in_date = ?");
        $stmt->bind_param("is", $employeeId, $clockInDate);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row) {
            return new TimeCard($row['employee_id'], $row['clock_in_date'], $row['clock_in_time'], $row['clock_out_date'], $row['clock_out_time'], $row['reported_hours'], $row['overtime']);
        }
        return null;
    }
}