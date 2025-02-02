<?php

require_once __DIR__ . '/fpdf/fpdf.php';


class PDFGenerator extends FPDF {
    public function header() {
        // Add a header to the PDF
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'PayStream Payslip', 0, 1, 'C');
        $this->Ln(10);
    }

    public function footer() {
        // Add a footer to the PDF
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    public function addPayslip($employee, $payroll) {
        $this->AddPage();
        $this->SetFont('Arial', '', 12);

        // Employee details
        $this->Cell(0, 10, 'Employee Name: ' . $employee['employee_name'], 0, 1);
        $this->Cell(0, 10, 'Employee ID: ' . $employee['employee_id'], 0, 1);
        $this->Cell(0, 10, 'Pay Period: ' . $payroll->getPayPeriodStart() . ' to ' . $payroll->getPayPeriodEnd(), 0, 1);
        $this->Ln(10);

        // Payroll details
        $this->Cell(0, 10, 'Hours Worked: ' . $payroll->getHoursWorked(), 0, 1);
        $this->Cell(0, 10, 'Overtime Hours: ' . $payroll->getOvertimeHours(), 0, 1);
        $this->Cell(0, 10, 'Total Earnings: Rs. ' . $payroll->getTotalEarnings(), 0, 1);
        $this->Cell(0, 10, 'Deductions: Rs. ' . $payroll->getDeductions(), 0, 1);
        $this->Cell(0, 10, 'Net Pay: Rs. ' . $payroll->getNetPay(), 0, 1);
    }
}