<?php

require_once __DIR__ . '/../models/Payroll.php';
require_once __DIR__ . '/../utils/Logger.php';

class PayrollRepository {
    private $dbHandler;
    private $logger;

    public function __construct($dbHandler) {
        $this->dbHandler = $dbHandler;
        $this->logger = new Logger(__DIR__ . '/../logs/payroll_repository.log');
    }

    public function save(Payroll $payroll) {
        $stmt = $this->dbHandler->prepare("INSERT INTO payroll (employee_id, pay_period_start, pay_period_end, hours_worked, overtime_hours, total_earnings, deductions, net_pay) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssdddd", $payroll->getEmployeeId(), $payroll->getPayPeriodStart(), $payroll->getPayPeriodEnd(), $payroll->getHoursWorked(), $payroll->getOvertimeHours(), $payroll->getTotalEarnings(), $payroll->getDeductions(), $payroll->getNetPay());
        if ($stmt->execute()) {
            $this->logger->log("Payroll saved: " . json_encode($payroll));
        } else {
            $this->logger->log("Error saving payroll: " . $stmt->error);
        }
    }

    public function findById($payrollId) {
        $this->logger->log("Finding payroll by ID: $payrollId");
        $stmt = $this->dbHandler->prepare("SELECT * FROM payroll WHERE payroll_id = ?");
        $stmt->bind_param("i", $payrollId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row) {
            $payroll = new Payroll($row['payroll_id'], $row['employee_id'], $row['pay_period_start'], $row['pay_period_end']);
            $payroll->calculatePayroll($row['employee_id'], $row['clockings']); // Assuming you have a method to get clockings
            return $payroll;
        }
        return null;
    }

    public function findByEmployeeIdAndPeriod($employeeId, $payPeriodStart, $payPeriodEnd) {
        $stmt = $this->dbHandler->prepare("SELECT * FROM payroll WHERE employee_id = ? AND pay_period_start = ? AND pay_period_end = ?");
        $stmt->bind_param("iss", $employeeId, $payPeriodStart, $payPeriodEnd);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row) {
            return new Payroll($row['payroll_id'], $row['employee_id'], $row['pay_period_start'], $row['pay_period_end']);
        }
        return null;
    }
}