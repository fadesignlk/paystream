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

    public function calculatePayroll($employee, $timecards) {
        $totalHours = 0;
        $totalOvertimeHours = 0;

        foreach ($timecards as $timecard) {
            $clockInTime = strtotime($timecard['clock_in_time']);
            $clockOutTime = strtotime($timecard['clock_out_time']);
            if ($clockInTime !== false && $clockOutTime !== false) {
                // If clock out time is earlier than clock in time, it means the work period spans midnight
                if ($clockOutTime < $clockInTime) {
                    $clockOutTime += 86400; // Add 24 hours in seconds
                }
                $hoursWorked = ($clockOutTime - $clockInTime) / 3600; // Convert seconds to hours
                $totalHours += $hoursWorked;

                if ($hoursWorked > 8) {
                    $totalOvertimeHours += $hoursWorked - 8;
                    $totalHours -= $hoursWorked - 8;
                }
            }
        }

        $this->hoursWorked = $totalHours;
        $this->overtimeHours = $totalOvertimeHours;

        $employeeRate = $employee['employee_rate'];
        if (is_numeric($employeeRate)) {
            $this->totalEarnings = ($this->hoursWorked * $employeeRate) + ($this->overtimeHours * $employeeRate * 1.5);
        } else {
            $this->totalEarnings = 0;
        }

        $this->netPay = $this->totalEarnings - $this->deductions;
    }

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