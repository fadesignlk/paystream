<?php

require_once __DIR__ . '/../repositories/TimeCardRepository.php';
require_once __DIR__ . '/../utils/Logger.php';

class TimeCardController {
    private $timeCardRepository;
    private $logger;

    public function __construct($dbHandler) {
        $this->timeCardRepository = new TimeCardRepository($dbHandler);
        $this->logger = new Logger(__DIR__ . '/../logs/timecard_controller.log');
    }

    public function processClocking($employeeId, $clockingDate, $clockingTime, $clockingType) {
        $this->logger->log("Processing clocking: employeeId=$employeeId, clockingDate=$clockingDate, clockingTime=$clockingTime, clockingType=$clockingType");
        if ($clockingType == 'In') {
            return $this->createTimeCard($employeeId, $clockingDate, $clockingTime);
        } else {
            return $this->updateTimeCard($employeeId, $clockingDate, $clockingTime);
        }
    }

    public function createTimeCard($employeeId, $clockInDate, $clockInTime) {
        $this->logger->log("Creating time card: employeeId=$employeeId, clockInDate=$clockInDate, clockInTime=$clockInTime");
        $timeCard = new TimeCard($employeeId, $clockInDate, $clockInTime);
        $result = $this->timeCardRepository->save($timeCard);
        if ($result) {
            $this->logger->log("Time card created successfully: " . json_encode($timeCard));
        } else {
            $this->logger->log("Error creating time card for employeeId=$employeeId, clockInDate=$clockInDate, clockInTime=$clockInTime");
        }
        return $result;
    }

    public function updateTimeCard($employeeId, $clockOutDate, $clockOutTime) {
        $this->logger->log("Updating time card: employeeId=$employeeId, clockOutDate=$clockOutDate, clockOutTime=$clockOutTime");
        $timeCard = $this->timeCardRepository->findMostRecentClockIn($employeeId);
        if ($timeCard) {
            $this->logger->log("Most recent clock-in found: " . json_encode($timeCard));
            $clockInDateTime = new DateTime($timeCard->getClockInDate() . ' ' . $timeCard->getClockInTime());
            $clockOutDateTime = new DateTime($clockOutDate . ' ' . $clockOutTime);
            $interval = $clockInDateTime->diff($clockOutDateTime);
            $reportedHours = $interval->format('%h hours %i minutes');
            $totalMinutes = ($interval->h * 60) + $interval->i;
            $overtime = '';
            if ($totalMinutes > 480) { // 480 minutes = 8 hours
                $overtimeMinutes = $totalMinutes - 480;
                $overtime = floor($overtimeMinutes / 60) . ' hours ' . ($overtimeMinutes % 60) . ' minutes';
            }
            $timeCard->setClockOutDate($clockOutDate);
            $timeCard->setClockOutTime($clockOutTime);
            $timeCard->setReportedHours($reportedHours);
            $timeCard->setOvertime($overtime);
            $result = $this->timeCardRepository->update($timeCard);
            if ($result) {
                $this->logger->log("Time card updated successfully: " . json_encode($timeCard));
            } else {
                $this->logger->log("Error updating time card for employeeId=$employeeId, clockOutDate=$clockOutDate, clockOutTime=$clockOutTime");
            }
            return $result;
        } else {
            $this->logger->log("No clock-in record found for employeeId=$employeeId to update with clockOutDate=$clockOutDate, clockOutTime=$clockOutTime");
            return false;
        }
    }

    public function getAllTimeCards() {
        $this->logger->log("Fetching all time cards");
        return $this->timeCardRepository->findAll();
    }
}