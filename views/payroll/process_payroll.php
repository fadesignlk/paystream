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
$payslip = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedMonth = $_POST['month'];
    $payPeriodStart = date('Y-m-01', strtotime($selectedMonth));
    $payPeriodEnd = date('Y-m-t', strtotime($selectedMonth));

    $result = $payrollController->processPayrollForAllEmployees($payPeriodStart, $payPeriodEnd);
    if ($result === true) {
        $successMessage = 'Payroll processed successfully. Payslips have been generated.';
    } else {
        $errors[] = $result;
    }
}

$title = 'Process Payroll';
include __DIR__ . '/../components/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
        <h1 class="mb-4">Process Payroll</h1>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul style="list-style-type: none; padding-left: 0;">
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
                <label for="month" class="form-label">Select Month</label>
                <input type="month" id="month" name="month" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Process Payroll</button>
        </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>