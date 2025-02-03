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

    public function addPayslip($employee, $payroll, $timecards) {
        $this->AddPage();

        // Employee details
        $this->SetFont('Arial', 'B', 13);
        $this->Cell(0, 10, 'Employee Details', 0, 1);

        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, 'Employee Name: ' . $employee['employee_name'], 0, 1);
        $this->Cell(0, 10, 'Employee ID: ' . $employee['employee_id'], 0, 1);
        $this->Cell(0, 10, 'Employee Pay Rate: ' . $employee['employee_rate'], 0, 1);
        $this->Cell(0, 10, 'Pay Period: ' . $payroll->getPayPeriodStart() . ' to ' . $payroll->getPayPeriodEnd(), 0, 1);
        $this->Ln(3);

        // Time card details
        $this->SetFont('Arial', 'B', 13);
        $this->Cell(0, 10, 'Time Card Details', 0, 1);

        $this->SetFont('Arial', '', 12);
        $this->Cell(40, 10, 'Date', 1);
        $this->Cell(40, 10, 'Clock In Time', 1);
        $this->Cell(40, 10, 'Clock Out Time', 1);
        $this->Cell(40, 10, 'Hours Worked', 1);
        $this->Ln();

        // Table rows
        foreach ($timecards as $timecard) {
            $clockInTime = strtotime($timecard['clock_in_time']);
            $clockOutTime = strtotime($timecard['clock_out_time']);
            if ($clockInTime !== false && $clockOutTime !== false) {
                // If clock out time is earlier than clock in time, it means the work period spans midnight
                if ($clockOutTime < $clockInTime) {
                    $clockOutTime += 86400; // Add 24 hours in seconds
                }
                $hoursWorked = ($clockOutTime - $clockInTime) / 3600; // Convert seconds to hours

                $this->Cell(40, 10, $timecard['clock_in_date'], 1);
                $this->Cell(40, 10, date('H:i', $clockInTime), 1);
                $this->Cell(40, 10, date('H:i', $clockOutTime), 1);
                $this->Cell(40, 10, number_format($hoursWorked, 2), 1);
                $this->Ln();
            }
        }
        $this->Ln(10);

        // Payroll details
        $this->SetFont('Arial', 'B', 13);
        $this->Cell(0, 10, 'Payroll Details', 0, 1);

        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, 'Hours Worked: ' . $payroll->getHoursWorked(), 0, 1);
        $this->Cell(0, 10, 'Overtime Hours: ' . $payroll->getOvertimeHours(), 0, 1);
        $this->Cell(0, 10, 'Total Earnings: Rs. ' . $payroll->getTotalEarnings(), 0, 1);
        $this->Cell(0, 10, 'Deductions: Rs. ' . $payroll->getDeductions(), 0, 1);
        $this->Cell(0, 10, 'Net Pay: Rs. ' . $payroll->getNetPay(), 0, 1);


    }

    public function addInvalidTimecardNotice($employee, $timecards, $payPeriodStart, $payPeriodEnd) {
        $this->AddPage();
        
        // Employee details
        $this->SetFont('Arial', 'B', 13);
        $this->Cell(0, 10, 'Employee Details', 0, 1);

        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, 'Employee Name: ' . $employee['employee_name'], 0, 1);
        $this->Cell(0, 10, 'Employee ID: ' . $employee['employee_id'], 0, 1);
        $this->Cell(0, 10, 'Pay Period: ' . $payPeriodStart . ' to ' . $payPeriodEnd, 0, 1);
        $this->Ln(10);

        // Invalid timecard notice
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Incomplete Timecard Records', 0, 1);
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, 'The following timecard records are invalid and cannot be processed:', 0, 1);
        $this->Ln(6);

        foreach ($timecards as $timecard) {
            $this->Cell(0, 10, 'Date: ' . $timecard['clock_in_date'] . ', Clock In Time: ' . $timecard['clock_in_time'] . ', Clock Out Time: ' . $timecard['clock_out_time'], 0, 1);
        }
    }
}