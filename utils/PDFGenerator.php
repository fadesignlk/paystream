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

    public function addInvalidTimecardNotice($employee, $timecards, $payPeriodStart, $payPeriodEnd) {
        $this->AddPage();
        $this->SetFont('Arial', '', 12);

        // Employee details
        $this->Cell(0, 10, 'Employee Name: ' . $employee['employee_name'], 0, 1);
        $this->Cell(0, 10, 'Employee ID: ' . $employee['employee_id'], 0, 1);
        $this->Cell(0, 10, 'Pay Period: ' . $payPeriodStart . ' to ' . $payPeriodEnd, 0, 1);
        $this->Ln(10);

        // Invalid timecard notice
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Invalid Timecard Records', 0, 1);
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, 'The following timecard records are invalid and cannot be processed:', 0, 1);
        $this->Ln(10);

        foreach ($timecards as $timecard) {
            $this->Cell(0, 10, 'Date: ' . $timecard['clock_in_date'] . ', Clock In: ' . $timecard['clock_in_time'] . ', Clock Out: ' . $timecard['clock_out_date'] . ', Reported Hours: ' . $timecard['reported_hours'], 0, 1);
        }
    }
}