<?php

require_once __DIR__ . '/../repositories/TimeCardRepository.php';

class TimeCardController {
    private $timeCardRepository;

    public function __construct($dbHandler) {
        $this->timeCardRepository = new TimeCardRepository($dbHandler);
    }

    public function processClocking($employeeId, $clockingDate, $clockingTime, $clockingType) {
        if ($clockingType == 'In') {
            return $this->createTimeCard($employeeId, $clockingDate, $clockingTime);
        } else {
            return $this->updateTimeCard($employeeId, $clockingDate, $clockingTime);
        }
    }

    public function createTimeCard($employeeId, $clockInDate, $clockInTime) {
        $timeCard = new TimeCard($employeeId, $clockInDate, $clockInTime);
        return $this->timeCardRepository->save($timeCard);
    }

    public function updateTimeCard($employeeId, $clockOutDate, $clockOutTime) {
        $timeCard = $this->timeCardRepository->findByEmployeeIdAndClockInDate($employeeId, $clockOutDate);
        if ($timeCard) {
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
            return $this->timeCardRepository->update($timeCard);
        }
        return false;
    }
}