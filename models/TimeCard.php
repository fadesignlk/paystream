<?php

class TimeCard {
    private $id;
    private $employeeId;
    private $clockInDate;
    private $clockOutDate;
    private $clockInTime;
    private $clockOutTime;
    private $reportedHours;
    private $overtime;

    public function __construct($employeeId, $clockInDate, $clockInTime, $clockOutDate = null, $clockOutTime = null, $reportedHours = null, $overtime = null) {
        $this->employeeId = $employeeId;
        $this->clockInDate = $clockInDate;
        $this->clockInTime = $clockInTime;
        $this->clockOutDate = $clockOutDate;
        $this->clockOutTime = $clockOutTime;
        $this->reportedHours = $reportedHours;
        $this->overtime = $overtime;
    }

    // Getters and setters for each property
    public function getId() {
        return $this->id;
    }

    public function getEmployeeId() {
        return $this->employeeId;
    }

    public function getClockInDate() {
        return $this->clockInDate;
    }

    public function getClockOutDate() {
        return $this->clockOutDate;
    }

    public function getClockInTime() {
        return $this->clockInTime;
    }

    public function getClockOutTime() {
        return $this->clockOutTime;
    }

    public function getReportedHours() {
        return $this->reportedHours;
    }

    public function getOvertime() {
        return $this->overtime;
    }

    public function setClockOutDate($clockOutDate) {
        $this->clockOutDate = $clockOutDate;
    }

    public function setClockOutTime($clockOutTime) {
        $this->clockOutTime = $clockOutTime;
    }

    public function setReportedHours($reportedHours) {
        $this->reportedHours = $reportedHours;
    }

    public function setOvertime($overtime) {
        $this->overtime = $overtime;
    }
}