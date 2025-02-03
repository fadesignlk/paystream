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
        $outputDir = __DIR__ . '/../output/';
        $pdfFilePath = $outputDir . 'payslips_' . date('Y_m', strtotime($payPeriodStart)) . '.pdf';
        $timecardsFound = false;

        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0777, true);
        }
        
        foreach ($employees as $employee) {
            $timecards = $this->timecardRepository->findByEmployeeIdAndPeriod($employee['employee_id'], $payPeriodStart, $payPeriodEnd);
            
            if (!empty($timecards)) {
                $this->logger->log("Processing payroll for employee {$employee['employee_id']} Pay Period: $payPeriodStart to $payPeriodEnd");
                $isValid = $this->validateTimecards($timecards);

                if ($isValid) {
                    $timecardsFound = true;
                    $payroll = new Payroll(null, $employee['employee_id'], $payPeriodStart, $payPeriodEnd);
                    $payroll->calculatePayroll($employee, $timecards);
                    $this->payrollRepository->save($payroll);

                    $pdf->addPayslip($employee, $payroll, $timecards);
                } else {
                    $pdf->addInvalidTimecardNotice($employee, $timecards, $payPeriodStart, $payPeriodEnd);
                }
            }
        }

        if ($timecardsFound) {
            $pdf->Output($pdfFilePath, 'F');
            return true;
        } else {
            $this->logger->log("No timecards found for the selected pay period: $payPeriodStart to $payPeriodEnd");
            return 'No timecards found for the selected pay period';
        }
        

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