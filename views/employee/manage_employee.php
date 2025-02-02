<?php
// manage_employees.php (List, Update, and Delete Employees)
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../../database/DatabaseHandler.php';
require_once __DIR__ . '/../../controllers/EmployeeController.php';
require_once __DIR__ . '/../../controllers/AuditLogController.php';

if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'views/login.php');
    exit();
}

$user = $_SESSION['user'];
$loggedInUserId = $user->getUserId(); // Get logged in User ID

$dbHandler = new DatabaseHandler(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$employeeController = new EmployeeController($dbHandler);
$auditLogController = new AuditLogController($dbHandler);

$errors = [];
$successMessage = '';

if (isset($_GET['delete'])) {
    $employeeIdToDelete = $_GET['delete'];
    if ($employeeController->deleteEmployee($employeeIdToDelete, $loggedInUserId)) {
        $successMessage = "Employee deleted successfully.";
        $auditLogController->logTransaction($loggedInUserId, 'Delete Employee', 'Employee ID: ' . $employeeIdToDelete);
    } else {
        $errors[] = "Error deleting employee.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_employee'])) {
    $dob = $_POST['dob']; // Make sure this is in YYYY-MM-DD format

    // If it's not, you might need to convert it:
    if (!empty($dob)) {
        $dob = date('Y-m-d', strtotime($_POST['dob']));
    } else {
        $dob = null; // Or some default value
    }

    $employeeId = $_POST['employee_id'];
    $name = $_POST['name'];
    $rate = $_POST['rate'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $type = $_POST['type'];
    $ni = $_POST['ni'];
    $paymentMethod = $_POST['payment_method'];
    $bankAccount = $_POST['bank_account'];
    $sortCode = $_POST['sort_code'];
    $status = $_POST['status'];

    // Validation (same as add_employee.php)
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (!is_numeric($rate) || $rate <= 0) {
        $errors[] = "Rate must be a positive number.";
    }

    if (empty($errors)) {
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

        if ($employeeController->updateEmployee($employeeId, $employeeData)) {
            $successMessage = "Employee updated successfully.";
            $auditLogController->logTransaction($loggedInUserId, 'Update Employee', 'Employee ID: ' . $employeeId);
        } else {
            $errors[] = "Error updating employee.";
        }
    }
}

$title = 'Manage Employees';
include __DIR__ . '/../components/header.php';
?>

<div class="container mt-4">
    <h1 class="mb-4">Manage Employees</h1>

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

    <!-- Search and Filter Form -->
    <form id="searchForm" method="GET" action="manage_employee.php" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" id="search" name="search" class="form-control" placeholder="Search by name" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
        </div>
        <div class="col-md-4">
            <select id="status" name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="1" <?php echo (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : ''; ?>>Active</option>
                <option value="0" <?php echo (isset($_GET['status']) && $_GET['status'] == '0') ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary">Search</button>
            <button type="button" class="btn btn-secondary" onclick="clearFilters()">Clear</button>
            <a href="add_employee.php" class="btn btn-success">Add Employee</a>
        </div>
    </form>

    <div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Type</th>
                <th>NIC Number</th>
                <th>Payment Method</th>
                <th>Bank Account Number</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="employeeTableBody">
            <?php
            // Fetch employees based on search and filter criteria
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            $status = isset($_GET['status']) ? $_GET['status'] : '';
            $employees = $employeeController->getEmployees($search, $status); // Use the getEmployees method
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
        </tbody>
    </table>
    </div>

</div>


<!-- Modal -->
<div id="editModal" class="modal fade" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalContent"></div>
        </div>
    </div>
</div>

<script>
    function clearFilters() {
        document.getElementById('search').value = '';
        document.getElementById('status').value = '';
        fetchEmployees();
    }

    function fetchEmployees() {
        var search = document.getElementById('search').value;
        var status = document.getElementById('status').value;

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                document.getElementById('employeeTableBody').innerHTML = xhr.responseText;
            }
        };
        xhr.open("GET", "fetch_employees.php?search=" + search + "&status=" + status, true);
        xhr.send();
    }

    document.getElementById('searchForm').addEventListener('submit', function(event) {
        event.preventDefault();
        fetchEmployees();
    });

    function updateEmployee() {
        var form = document.getElementById('editEmployeeForm');
        var formData = new FormData(form);

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "update_employee.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                closeModal();
                fetchEmployees();
            }
        };
        xhr.send(formData);
    }

    function openModal(employeeId) {
        var modal = new bootstrap.Modal(document.getElementById('editModal'));
        var modalContent = document.getElementById("modalContent");

        // Fetch the edit form using AJAX
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                modalContent.innerHTML = xhr.responseText;
                modal.show();
            }
        };
        xhr.open("GET", "get_employee_form.php?id=" + employeeId, true);
        xhr.send();
    }

    function closeModal() {
        var modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
        modal.hide();
    }
</script>

<?php include __DIR__ . '/../components/footer.php'; ?>