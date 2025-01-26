<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../controllers/EmployeeController.php';
require_once __DIR__ . '/../database/DatabaseHandler.php';

$dbHandler = new DatabaseHandler(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$employeeController = new EmployeeController($dbHandler);

if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'views/login.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employeeId = $_POST['employee_id'];
    $employeeData = [
        'employee_name' => $_POST['employee_name'],
        'employee_rate' => $_POST['employee_rate'],
        'employee_address' => $_POST['employee_address'],
        'employee_contact' => $_POST['employee_contact'],
        'employee_type' => $_POST['employee_type'],
        'date_of_birth' => $_POST['date_of_birth'],
        'national_insurance_number' => $_POST['national_insurance_number'],
        'payment_method' => $_POST['payment_method'],
        'bank_account_number' => $_POST['bank_account_number'],
        'sort_code' => $_POST['sort_code'],
        'status' => $_POST['status']
    ];
    $employeeController->updateEmployee($employeeId, $employeeData);
}
?>