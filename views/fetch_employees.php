<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../database/DatabaseHandler.php';
require_once __DIR__ . '/../controllers/EmployeeController.php';

$dbHandler = new DatabaseHandler(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$employeeController = new EmployeeController($dbHandler);

if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'views/login.php');
    exit();
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

$employees = $employeeController->getEmployees($search, $status);

foreach ($employees as $employee): ?>
    <tr>
        <td><?php echo htmlspecialchars($employee['employee_id']); ?></td>
        <td><?php echo htmlspecialchars($employee['employee_name']); ?></td>
        <td><?php echo htmlspecialchars($employee['employee_contact']); ?></td>
        <td><?php echo htmlspecialchars($employee['employee_type']); ?></td>
        <td><?php echo htmlspecialchars($employee['national_insurance_number']); ?></td>
        <td><?php echo htmlspecialchars($employee['payment_method']); ?></td>
        <td><?php echo htmlspecialchars($employee['bank_account_number']); ?></td>
        <td><?php echo $employee['status'] == 1 ? 'Active' : 'Inactive'; ?></td>
        <td>
            <button class="btn btn-warning btn-sm" onclick="openModal(<?php echo $employee['employee_id']; ?>)">Edit</button>
            <a href="?delete=<?php echo $employee['employee_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this employee?')">Delete</a>
        </td>
    </tr>
<?php endforeach; ?>