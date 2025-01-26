<?php
class Database {
    private $conn;

    public function __construct($host, $username, $password, $dbname) {
        $this->conn = new mysqli($host, $username, $password, $dbname);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function query($sql) {
        return $this->conn->query($sql);
    }

    public function storeClocking(Clocking $clocking, $updatedBy) {
        $sql = "INSERT INTO Clockings (employee_id, clocking_date, clocking_time, clocking_type, updated_by) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isssi", $clocking->getEmployeeId(), $clocking->getClockingDate(), $clocking->getClockingTime(), $clocking->getClockingType(), $updatedBy);
        $stmt->execute();
    }

    public function getClockingsForPeriod($employeeId, $start, $end) {
        $sql = "SELECT * FROM Clockings WHERE employee_id = ? AND clocking_date BETWEEN ? AND ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iss", $employeeId, $start, $end);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getEmployees($search = '', $status = '') {
        $query = "SELECT * FROM employees WHERE 1=1";
        if ($search) {
            $query .= " AND employee_name LIKE ?";
        }
        if ($status !== '') {
            $query .= " AND status = ?";
        }
        $stmt = $this->conn->prepare($query);
        if ($search && $status !== '') {
            $search = '%' . $search . '%';
            $stmt->bind_param("ss", $search, $status);
        } elseif ($search) {
            $search = '%' . $search . '%';
            $stmt->bind_param("s", $search);
        } elseif ($status !== '') {
            $stmt->bind_param("s", $status);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllEmployees() {
        $sql = "SELECT * FROM Employees";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getEmployeeById($employeeId) {
        $stmt = $this->conn->prepare("SELECT * FROM Employees WHERE employee_id = ?");
        $stmt->bind_param("i", $employeeId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateEmployee(Employee $employee, $updatedBy) {
        $sql = "UPDATE Employees SET employee_name = ?, employee_rate = ?, employee_address = ?, employee_contact = ?, employee_type = ?, date_of_birth = ?, national_insurance_number = ?, payment_method = ?, bank_account_number = ?, sort_code = ?, updated_by = ? WHERE employee_id = ?";
        $stmt = $this->conn->prepare($sql);
    
        $dob = $employee->getDob();
        if (empty($dob)) {
            $dob = null; // Set to null if empty
        } else {
            $dob = date('Y-m-d', strtotime($dob)); //Format the date
        }
        $address = $employee->getAddress();
        if (empty($address)) {
            $address = null;
        }
        $contact = $employee->getContact();
        if (empty($contact)) {
            $contact = null;
        }
        $type = $employee->getType();
        if (empty($type)) {
            $type = null;
        }
        $ni = $employee->getNi();
        if (empty($ni)) {
            $ni = null;
        }
        $paymentMethod = $employee->getPaymentMethod();
        if (empty($paymentMethod)) {
            $paymentMethod = null;
        }
        $bankAccount = $employee->getBankAccount();
        if (empty($bankAccount)) {
            $bankAccount = null;
        }
        $sortCode = $employee->getSortCode();
        if (empty($sortCode)) {
            $sortCode = null;
        }
        $name = $employee->getName();
        $rate = $employee->getRate();
        $employeeId = $employee->getEmployeeId();
    
    
    
        $stmt->bind_param("sdssssssssii", $name, $rate, $address, $contact, $type, $dob, $ni, $paymentMethod, $bankAccount, $sortCode, $updatedBy, $employeeId);
        $query = str_replace("?", "'%s'", $sql);
        $query = vsprintf($query, array_map(array($this->conn, 'real_escape_string'), array($name, $rate, $address, $contact, $type, $dob, $ni, $paymentMethod, $bankAccount, $sortCode, $updatedBy, $employeeId)));
        error_log($query);
        $result = $stmt->execute();
        if($result === false){
            error_log($stmt->error);
        }
        return $result;
    }

    public function deleteEmployee($employeeId, $updatedBy) {
        $sql = "UPDATE Employees SET status = 0, updated_by = ? WHERE employee_id = ?"; //Soft Delete
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $updatedBy, $employeeId);
        return $stmt->execute();
    }

    public function storePayroll(Payroll $payroll, $updatedBy) {
        $sql = "INSERT INTO Payroll (employee_id, pay_period_start, pay_period_end, payroll_date, hours_worked, overtime_hours, total_earnings, deductions, net_pay, updated_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("issddddddi", $payroll->getEmployeeId(), $payroll->getPayPeriodStart(), $payroll->getPayPeriodEnd(), date('Y-m-d'), $payroll->getHoursWorked(), $payroll->getOvertimeHours(), $payroll->getTotalEarnings(), $payroll->getDeductions(), $payroll->getNetPay(), $updatedBy);
        $stmt->execute();
    }
    
    public function getUserByUsername($username) {
        $stmt = $this->conn->prepare("SELECT * FROM Users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }


    public function logTransaction($userId, $action, $details = '') {
        $sql = "INSERT INTO audit_logs (user_id, action, details) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iss", $userId, $action, $details);
        $stmt->execute();
    }
}
?>