<?php
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../models/AuditLog.php';
require_once __DIR__ . '/../database/DatabaseHandler.php';

class EmployeeController {
    private $employeeModel;
    private $auditLogModel;

    public function __construct($dbHandler) {
        $this->employeeModel = new Employee($dbHandler);
        $this->auditLogModel = new AuditLog($dbHandler);
    }

    public function getAllEmployees() {
        return $this->employeeModel->getAllEmployees();
    }

    public function getEmployees($search = '', $status = '') {
        return $this->employeeModel->getEmployees($search, $status);
    }

    public function getEmployeeById($employeeId) {
        return $this->employeeModel->getEmployeeById($employeeId);
    }

    public function getEmployeeName($employeeId) {
        return $this->employeeModel->getName($employeeId);
    }

    public function addEmployee($employeeData, $updatedBy) {
        return $this->employeeModel->addEmployee($employeeData, $updatedBy);
    }

    public function updateEmployee($employeeId, $employeeData) {
        return $this->employeeModel->updateEmployee($employeeId, $employeeData);
    }

    public function deleteEmployee($employeeId, $updatedBy) {
        if ($this->employeeModel->deleteEmployee($employeeId, $updatedBy)) {
            $this->auditLogModel->logTransaction($updatedBy, 'Delete Employee', 'Employee ID: ' . $employeeId);
            return true;
        } else {
            return false;
        }
    }

    public function fetchEmployees($search = '', $status = '') {
        $employees = $this->getEmployees($search, $status);
        foreach ($employees as $employee) {
            echo '<tr>';
            echo '<td>' . $employee['employee_id'] . '</td>';
            echo '<td>' . $employee['employee_name'] . '</td>';
            echo '<td>' . $employee['employee_rate'] . '</td>';
            echo '<td>' . ($employee['status'] == 1 ? 'Active' : 'Inactive') . '</td>';
            echo '<td>';
            echo '<button onclick="openModal(' . $employee['employee_id'] . ')">Edit</button>';
            echo '<a href="?delete=' . $employee['employee_id'] . '" onclick="return confirm(\'Are you sure you want to delete this employee?\')">Delete</a>';
            echo '</td>';
            echo '</tr>';
        }
    }
}


?>