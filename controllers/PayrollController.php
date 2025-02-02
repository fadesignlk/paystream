<?php

require_once __DIR__ . '/../repositories/PayrollRepository.php';
require_once __DIR__ . '/../repositories/TimecardRepository.php';
require_once __DIR__ . '/../repositories/EmployeeRepository.php';
require_once __DIR__ . '/../models/Payroll.php';
require_once __DIR__ . '/../utils/Logger.php';
require_once __DIR__ . '/../utils/PDFGenerator.php';

class PayrollController {
    private $payrollRepository;
    private $timecardRepository;
    private $employeeRepository;
    private $logger;

    public function __construct($dbHandler) {
        $this->payrollRepository = new PayrollRepository($dbHandler);
        $this->timecardRepository = new TimeCardRepository($dbHandler);
        $this->employeeRepository = new EmployeeRepository($dbHandler);
        $this->logger = new Logger(__DIR__ . '/../logs/payroll_controller.log');
    }

    public function processPayrollForAllEmployees($payPeriodStart, $payPeriodEnd) {
        $employees = $this->employeeRepository->getAllEmployees();
        $pdf = new PDFGenerator();
        
        foreach ($employees as $employee) {
            $timecards = $this->timecardRepository->findByEmployeeIdAndPeriod($employee['employee_id'], $payPeriodStart, $payPeriodEnd);
            if (!empty($timecards)) {
                $this->logger->log("Processing payroll for employee  Pay Period: $payPeriodStart to $payPeriodEnd");
                $isValid = $this->validateTimecards($timecards);

                if ($isValid) {
                    $payroll = new Payroll(null, $employee['employee_id'], $payPeriodStart, $payPeriodEnd);
                    $payroll->calculatePayroll($employee, $timecards);
                    $this->payrollRepository->save($payroll);
                    $pdf->addPayslip($employee, $payroll);
                } else {
                    $pdf->addInvalidTimecardNotice($employee, $timecards, $payPeriodStart, $payPeriodEnd);
                }
            }
        }

        return $pdf->output('S');
    }

    private function validateTimecards($timecards) {
        foreach ($timecards as $timecard) {
            if (empty($timecard['clock_in_date']) || empty($timecard['clock_out_date']) || empty($timecard['reported_hours'])) {
                return false; // Invalid timecard
            }
        }
        return true;
    }

}