<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../../database/DatabaseHandler.php';
require_once __DIR__ . '/../../controllers/EmployeeController.php';

$dbHandler = new DatabaseHandler(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$employeeController = new EmployeeController($dbHandler);

if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'views/login.php');
    exit();
}


if (isset($_GET['id'])) {
    $employeeId = $_GET['id'];
    $employee = $employeeController->getEmployeeById($employeeId);

    if ($employee) {
        ?>
        <form method="post" action="manage_employee.php">
            <input type="hidden" name="employee_id" value="<?php echo htmlspecialchars($employee['employee_id']); ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($employee['employee_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="rate" class="form-label">Rate</label>
                <input type="number" id="rate" name="rate" class="form-control" value="<?php echo htmlspecialchars($employee['employee_rate']); ?>" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" id="address" name="address" class="form-control" value="<?php echo htmlspecialchars($employee['employee_address']); ?>">
            </div>
            <div class="mb-3">
                <label for="contact" class="form-label">Contact</label>
                <input type="text" id="contact" name="contact" class="form-control" value="<?php echo htmlspecialchars($employee['employee_contact']); ?>">
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Type</label>
                <select id="type" name="type" class="form-select">
                    <option value="Permanent" <?php echo $employee['employee_type'] == 'Permanent' ? 'selected' : ''; ?>>Permanent</option>
                    <option value="Temporary" <?php echo $employee['employee_type'] == 'Temporary' ? 'selected' : ''; ?>>Temporary</option>
                    <option value="Contract" <?php echo $employee['employee_type'] == 'Contract' ? 'selected' : ''; ?>>Contract</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="dob" class="form-label">Date of Birth</label>
                <input type="date" id="dob" name="dob" class="form-control" value="<?php echo htmlspecialchars($employee['date_of_birth']); ?>">
            </div>
            <div class="mb-3">
                <label for="ni" class="form-label">National Insurance Number</label>
                <input type="text" id="ni" name="ni" class="form-control" value="<?php echo htmlspecialchars($employee['national_insurance_number']); ?>">
            </div>
            <div class="mb-3">
                <label for="payment_method" class="form-label">Payment Method</label>
                <select id="payment_method" name="payment_method" class="form-select">
                    <option value="Bank Transfer" <?php echo $employee['payment_method'] == 'Bank Transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                    <option value="Cheque" <?php echo $employee['payment_method'] == 'Cheque' ? 'selected' : ''; ?>>Cheque</option>
                    <option value="Cash" <?php echo $employee['payment_method'] == 'Cash' ? 'selected' : ''; ?>>Cash</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="bank_account" class="form-label">Bank Account Number</label>
                <input type="text" id="bank_account" name="bank_account" class="form-control" value="<?php echo htmlspecialchars($employee['bank_account_number']); ?>">
            </div>
            <div class="mb-3">
                <label for="sort_code" class="form-label">Sort Code</label>
                <input type="text" id="sort_code" name="sort_code" class="form-control" value="<?php echo htmlspecialchars($employee['sort_code']); ?>">
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="1" <?php echo $employee['status'] == 1 ? 'selected' : ''; ?>>Active</option>
                    <option value="0" <?php echo $employee['status'] == 0 ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <button type="submit" name="update_employee" class="btn btn-primary">Update Employee</button>
        </form>
        <?php
    } else {
        echo 'Employee not found.';
    }
} else {
    echo 'Invalid request.';
}
?>