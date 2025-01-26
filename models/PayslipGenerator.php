<?php


// classes/PayslipGenerator.php
class PayslipGenerator {
    public function generatePdf(Payroll $payroll, Employee $employee) {
        // This is a placeholder. You'll need a PDF library.
        echo "Payslip generated for employee: " . $employee->getName() . "<br>";
        echo "Pay Period: " . $payroll->getPayPeriodStart() . " to " . $payroll->getPayPeriodEnd() . "<br>";
        echo "Hours Worked: " . $payroll->getHoursWorked() . "<br>";
        echo "Overtime Hours: " . $payroll->getOvertimeHours() . "<br>";
        echo "Total Earnings: " . $payroll->getTotalEarnings() . "<br>";
        echo "Net Pay: " . $payroll->getNetPay() . "<br><br>";
    }
}
?>