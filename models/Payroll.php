<?php


class Payroll {
    private $payrollId;
    private $employeeId;
    private $payPeriodStart;
    private $payPeriodEnd;
    private $hoursWorked = 0;
    private $overtimeHours = 0;
    private $totalEarnings = 0;
    private $deductions = 0;
    private $netPay = 0;

    public function __construct($payrollId, $employeeId, $payPeriodStart, $payPeriodEnd) {
        $this->payrollId = $payrollId;
        $this->employeeId = $employeeId;
        $this->payPeriodStart = $payPeriodStart;
        $this->payPeriodEnd = $payPeriodEnd;
    }

    public function calculatePayroll($employee, $clockings) {
        $totalSeconds = 0;
        $lastClockIn = null;
        foreach ($clockings as $clocking) {
            if ($clocking['clocking_type'] == 'In') {
                $lastClockIn = strtotime($clocking['clocking_time']);
            } elseif ($clocking['clocking_type'] == 'Out' && $lastClockIn !== null) {
                $clockOut = strtotime($clocking['clocking_time']);
                $totalSeconds += ($clockOut - $lastClockIn);
                $lastClockIn = null;
            }
        }

        $this->hoursWorked = $totalSeconds / 3600;

        if ($this->hoursWorked > 8) {
            $this->overtimeHours = $this->hoursWorked - 8;
            $this->hoursWorked = 8;
        }

        $this->totalEarnings = ($this->hoursWorked * $employee['employee_rate']) + ($this->overtimeHours * $employee['employee_rate'] * 1.5);
        $this->netPay = $this->totalEarnings - $this->deductions;
    }

    // public function generatePayslip($employee) {
    //     $pdf = new FPDF();
    //     $pdf->addPage();
    //     $pdf->setFont('Arial', 'B', 15);
    //     $pdf->cell(0, 10, 'Payslip for ' . $employee['employee_name'], 0, 1, 'C');
    //     $pdf->setFont('Arial', '', 10);
    //     $pdf->cell(0, 10, 'Employee ID: ' . $this->employeeId, 0, 1);
    //     $pdf->cell(0, 10, 'Pay Period: ' . $this->payPeriodStart . ' to ' . $this->payPeriodEnd, 0, 1);
    //     $pdf->cell(0, 10, 'Hours Worked: ' . $this->hoursWorked, 0, 1);
    //     $pdf->cell(0, 10, 'Overtime Hours: ' . $this->overtimeHours, 0, 1);
    //     $pdf->cell(0, 10, 'Total Earnings: Rs. ' . $this->totalEarnings, 0, 1);
    //     $pdf->cell(0, 10, 'Deductions: Rs. ' . $this->deductions, 0, 1);
    //     $pdf->cell(0, 10, 'Net Pay: Rs. ' . $this->netPay, 0, 1);
    //     return $pdf->output('S');
    // }

    public function toArray() {
        return [
            'payroll_id' => $this->payrollId,
            'employee_id' => $this->employeeId,
            'pay_period_start' => $this->payPeriodStart,
            'pay_period_end' => $this->payPeriodEnd,
            'hours_worked' => $this->hoursWorked,
            'overtime_hours' => $this->overtimeHours,
            'total_earnings' => $this->totalEarnings,
            'deductions' => $this->deductions,
            'net_pay' => $this->netPay,
        ];
    }

    public function getPayrollId() { return $this->payrollId; }
    public function getEmployeeId() { return $this->employeeId; }
    public function getPayPeriodStart() { return $this->payPeriodStart; }
    public function getPayPeriodEnd() { return $this->payPeriodEnd; }
    public function getHoursWorked() { return $this->hoursWorked; }
    public function getOvertimeHours() { return $this->overtimeHours; }
    public function getTotalEarnings() { return $this->totalEarnings; }
    public function getDeductions() { return $this->deductions; }
    public function getNetPay() { return $this->netPay; }

    public function setPayrollId($payrollId){$this->payrollId = $payrollId;}
    public function setHoursWorked($hoursWorked){$this->hoursWorked = $hoursWorked;}
    public function setOvertimeHours($overtimeHours){$this->overtimeHours = $overtimeHours;}
    public function setTotalEarnings($totalEarnings){$this->totalEarnings = $totalEarnings;}
    public function setDeductions($deductions){$this->deductions = $deductions;}
    public function setNetPay($netPay){$this->netPay = $netPay;}
}

?>