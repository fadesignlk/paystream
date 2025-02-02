<?php

require_once __DIR__ . '/../repositories/PayrollRepository.php';
require_once __DIR__ . '/../repositories/ClockingRepository.php';
require_once __DIR__ . '/../repositories/EmployeeRepository.php';
require_once __DIR__ . '/../models/Payroll.php';
require_once __DIR__ . '/../utils/Logger.php';

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

    public function processPayroll($employeeId, $payPeriodStart, $payPeriodEnd) {
        $this->logger->log("Processing payroll for employee ID: $employeeId, Pay Period: $payPeriodStart to $payPeriodEnd");
        $employee = $this->employeeRepository->getEmployeeById($employeeId);
        $clockings = $this->clockingRepository->findByEmployeeIdAndPeriod($employeeId, $payPeriodStart, $payPeriodEnd);

        $payroll = new Payroll(null, $employeeId, $payPeriodStart, $payPeriodEnd);
        $payroll->calculatePayroll($employee, $clockings);
        $this->payrollRepository->save($payroll);

        return $payroll;
    }

    public function generatePayslip($payrollId) {
        $payroll = $this->payrollRepository->findById($payrollId);
        $employee = $this->employeeRepository->getEmployeeById($payroll->getEmployeeId());
        return $payroll->generatePayslip($employee);
    }
}