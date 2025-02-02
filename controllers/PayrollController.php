<?php

require_once __DIR__ . '/../repositories/PayrollRepository.php';
require_once __DIR__ . '/../repositories/ClockingRepository.php';
require_once __DIR__ . '/../repositories/EmployeeRepository.php';
require_once __DIR__ . '/../models/Payroll.php';
require_once __DIR__ . '/../utils/Logger.php';
require_once __DIR__ . '/../utils/PDFGenerator.php';

class PayrollController {
    private $payrollRepository;
    private $clockingRepository;
    private $employeeRepository;
    private $logger;

    public function __construct($dbHandler) {
        $this->payrollRepository = new PayrollRepository($dbHandler);
        $this->clockingRepository = new ClockingRepository($dbHandler);
        $this->employeeRepository = new EmployeeRepository($dbHandler);
        $this->logger = new Logger(__DIR__ . '/../logs/payroll_controller.log');
    }

    public function processPayrollForAllEmployees($payPeriodStart, $payPeriodEnd) {
        $this->logger->log("Processing payroll for all employees, Pay Period: $payPeriodStart to $payPeriodEnd");
        $employees = $this->employeeRepository->getAllEmployees();
        $pdf = new PDFGenerator();

        foreach ($employees as $employee) {
            $clockings = $this->clockingRepository->findByEmployeeIdAndPeriod($employee['employee_id'], $payPeriodStart, $payPeriodEnd);
            $payroll = new Payroll(null, $employee['employee_id'], $payPeriodStart, $payPeriodEnd);
            $payroll->calculatePayroll($employee, $clockings);
            $this->payrollRepository->save($payroll);
            $pdf->addPayslip($employee, $payroll);
        }

        return $pdf->output('S');
    }

    // public function generatePayslip($payrollId) {
    //     $payroll = $this->payrollRepository->findById($payrollId);
    //     $employee = $this->employeeRepository->getEmployeeById($payroll->getEmployeeId());
    //     return $payroll->generatePayslip($employee);
    // }
}