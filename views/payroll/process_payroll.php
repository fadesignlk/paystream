<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../../database/DatabaseHandler.php';
require_once __DIR__ . '/../../controllers/PayrollController.php';
require_once __DIR__ . '/../../controllers/EmployeeController.php';


if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'views/login.php');
    exit();
}

$dbHandler = new DatabaseHandler(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$payrollController = new PayrollController($dbHandler);
$employeeController = new EmployeeController($dbHandler);

$errors = [];
$successMessage = '';
$employees = $employeeController->getAllEmployees();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employeeId = $_POST['employee_id'];
    $payPeriodStart = $_POST['pay_period_start'];
    $payPeriodEnd = $_POST['pay_period_end'];

    $payroll = $payrollController->processPayroll($employeeId, $payPeriodStart, $payPeriodEnd);
    if ($payroll) {
        $successMessage = 'Payroll processed successfully.';
    } else {
        $errors[] = 'Error processing payroll.';
    }
}

$title = 'Process Payroll';
include __DIR__ . '/../components/header.php';
?>

<div class="container mt-4">
    <h1 class="mb-4">Process Payroll</h1>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success">
            <?php echo $successMessage; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="process_payroll.php">
        <div class="mb-3">
            <label for="employee_id" class="form-label">Employee</label>
            <select id="employee_id" name="employee_id" class="form-select" required>
                <?php
                $employees = $employeeController->getAllEmployees();
                foreach ($employees as $employee) {
                    echo '<option value="' . htmlspecialchars($employee['employee_id']) . '">' . htmlspecialchars($employee['employee_name']) . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="pay_period_start" class="form-label">Pay Period Start</label>
            <input type="date" id="pay_period_start" name="pay_period_start" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="pay_period_end" class="form-label">Pay Period End</label>
            <input type="date" id="pay_period_end" name="pay_period_end" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Process Payroll</button>
    </form>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>