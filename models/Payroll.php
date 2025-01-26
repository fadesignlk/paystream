<?php


// classes/Payroll.php
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

        $this->totalEarnings = ($this->hoursWorked * $employee->getRate()) + ($this->overtimeHours * $employee->getRate() * 1.5);
        $this->netPay = $this->totalEarnings - $this->deductions;
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

    public function setHoursWorked($hoursWorked){$this->hoursWorked = $hoursWorked;}
    public function setOvertimeHours($overtimeHours){$this->overtimeHours = $overtimeHours;}
    public function setTotalEarnings($totalEarnings){$this->totalEarnings = $totalEarnings;}
    public function setDeductions($deductions){$this->deductions = $deductions;}
    public function setNetPay($netPay){$this->netPay = $netPay;}
}

?>