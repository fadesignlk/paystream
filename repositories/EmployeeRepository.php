<?php
require_once '../database/DatabaseHandler.php';

class EmployeeRepository {
    private $dbHandler;

    public function __construct($dbHandler) {
        $this->dbHandler = $dbHandler;
    }

    public function getAllEmployees() {
        $query = "SELECT * FROM employees";
        $stmt = $this->dbHandler->prepare($query);
        $this->dbHandler->execute($stmt);
        $result = $this->dbHandler->getResult($stmt);
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
        $stmt = $this->dbHandler->prepare($query);
        if ($search && $status !== '') {
            $search = '%' . $search . '%';
            $stmt->bind_param("ss", $search, $status);
        } elseif ($search) {
            $search = '%' . $search . '%';
            $stmt->bind_param("s", $search);
        } elseif ($status !== '') {
            $stmt->bind_param("s", $status);
        }
        $this->dbHandler->execute($stmt);
        $result = $this->dbHandler->getResult($stmt);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getEmployeeById($employeeId) {
        $query = "SELECT * FROM employees WHERE employee_id = ?";
        $stmt = $this->dbHandler->prepare($query);
        $stmt->bind_param("i", $employeeId);
        $this->dbHandler->execute($stmt);
        $result = $this->dbHandler->getResult($stmt);
        return $result->fetch_assoc();
    }

    public function addEmployee($employeeData, $updatedBy) {
        $query = "INSERT INTO employees (employee_name, employee_rate, employee_address, employee_contact, employee_type, date_of_birth, national_insurance_number, payment_method, bank_account_number, sort_code, status, updated_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->dbHandler->prepare($query);
        $stmt->bind_param("sdssssssssii", $employeeData['employee_name'], $employeeData['employee_rate'], $employeeData['employee_address'], $employeeData['employee_contact'], $employeeData['employee_type'], $employeeData['date_of_birth'], $employeeData['national_insurance_number'], $employeeData['payment_method'], $employeeData['bank_account_number'], $employeeData['sort_code'], $employeeData['status'], $updatedBy);
        return $this->dbHandler->execute($stmt);
    }

    public function updateEmployee($employeeId, $employeeData) {
        $query = "UPDATE employees SET employee_name = ?, employee_rate = ?, employee_address = ?, employee_contact = ?, employee_type = ?, date_of_birth = ?, national_insurance_number = ?, payment_method = ?, bank_account_number = ?, sort_code = ?, status = ? WHERE employee_id = ?";
        $stmt = $this->dbHandler->prepare($query);
        $stmt->bind_param("sdssssssssii", $employeeData['employee_name'], $employeeData['employee_rate'], $employeeData['employee_address'], $employeeData['employee_contact'], $employeeData['employee_type'], $employeeData['date_of_birth'], $employeeData['national_insurance_number'], $employeeData['payment_method'], $employeeData['bank_account_number'], $employeeData['sort_code'], $employeeData['status'], $employeeId);
        return $this->dbHandler->execute($stmt);
    }

    public function deleteEmployee($employeeId, $updatedBy) {
        $sql = "UPDATE employees SET status = 0, updated_by = ? WHERE employee_id = ?"; // Soft Delete
        $stmt = $this->dbHandler->prepare($sql);
        $stmt->bind_param("ii", $updatedBy, $employeeId);
        return $this->dbHandler->execute($stmt);
    }

    // Add other employee-related database methods here
}


?>