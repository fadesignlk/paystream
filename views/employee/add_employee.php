<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../../database/DatabaseHandler.php';
require_once __DIR__ . '/../../controllers/EmployeeController.php';
require_once __DIR__ . '/../../controllers/AuditLogController.php';


$dbHandler = new DatabaseHandler(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$employeeController = new EmployeeController($dbHandler);
$auditLogController = new AuditLogController($dbHandler);

if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'views/login.php');
    exit();
}

$user = $_SESSION['user'];
$loggedInUserId = $user->getUserId(); // Get logged in User ID

$errors = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_employee'])) {
    $name = $_POST['employee_name'];
    $rate = $_POST['employee_rate'];
    $address = $_POST['employee_address'];
    $contact = $_POST['employee_contact'];
    $type = $_POST['employee_type'];
    $dob = $_POST['date_of_birth'];
    $ni = $_POST['national_insurance_number'];
    $paymentMethod = $_POST['payment_method'];
    $bankAccount = $_POST['bank_account_number'];
    $sortCode = $_POST['sort_code'];
    $status = $_POST['status'];

    $employeeData = [
        'employee_name' => $name,
        'employee_rate' => $rate,
        'employee_address' => $address,
        'employee_contact' => $contact,
        'employee_type' => $type,
        'date_of_birth' => $dob,
        'national_insurance_number' => $ni,
        'payment_method' => $paymentMethod,
        'bank_account_number' => $bankAccount,
        'sort_code' => $sortCode,
        'status' => $status
    ];

    if ($employeeController->addEmployee($employeeData, $loggedInUserId)) {
        $successMessage = 'Employee added successfully.';
        $auditLogController->logTransaction($_SESSION['user']->getUserId(), 'Add Employee', 'Employee Name: ' . $name);
    } else {
        $errors[] = 'Error adding employee.';
    }
}

$title = 'Add Employee';
include __DIR__ . '/../components/header.php';
?>

<div class="container mt-4">
    <div class="row">
            <h1 class="mb-4">Add Employee</h1>
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

            <form method="post" action="add_employee.php">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="employee_name" class="form-label">Name</label>
                            <input type="text" id="employee_name" name="employee_name" class="form-control" required tabindex="1">
                        </div>
                        <div class="mb-3">
                            <label for="employee_rate" class="form-label">Rate</label>
                            <input type="number" id="employee_rate" name="employee_rate" class="form-control" step="1" min="1" max="5">
                        </div>
                        <div class="mb-3">
                            <label for="employee_address" class="form-label">Address</label>
                            <input type="text" id="employee_address" name="employee_address" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="employee_contact" class="form-label">Contact</label>
                            <input type="text" id="employee_contact" name="employee_contact" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="employee_type" class="form-label">Type</label>
                            <select id="employee_type" name="employee_type" class="form-select"  tabindex="3">
                                <option value="Permanent">Permanent</option>
                                <option value="Temporary">Temporary</option>
                                <option value="Contract">Contract</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" id="date_of_birth" name="date_of_birth" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="national_insurance_number" class="form-label">NIC Number</label>
                            <input type="text" id="national_insurance_number" name="national_insurance_number" class="form-control" required tabindex="2">
                        </div>
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select id="payment_method" name="payment_method" class="form-select">
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cheque">Cheque</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="bank_account_number" class="form-label">Bank Account Number</label>
                            <input type="text" id="bank_account_number" name="bank_account_number" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="sort_code" class="form-label">Sort Code</label>
                            <input type="text" id="sort_code" name="sort_code" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" name="status" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <button type="submit" name="add_employee" class="btn btn-primary">Add Employee</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>