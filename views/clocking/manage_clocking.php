<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../../database/DatabaseHandler.php';
require_once __DIR__ . '/../../controllers/ClockingController.php';
require_once __DIR__ . '/../../controllers/EmployeeController.php';
require_once __DIR__ . '/../../controllers/AuditLogController.php';
require_once __DIR__ . '/../../controllers/TimeCardController.php';

$dbHandler = new DatabaseHandler(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$clockingController = new ClockingController($dbHandler);
$employeeController = new EmployeeController($dbHandler);
$auditLogController = new AuditLogController($dbHandler);
$timeCardController = new TimeCardController($dbHandler);

if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'views/login.php');
    exit();
}

$user = $_SESSION['user'];
$loggedInUserId = $user->getUserId(); // Get logged in User ID

$errors = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_clocking'])) {
    $employeeId = $_POST['employee_id'];
    $clockingDate = $_POST['clocking_date'];
    $clockingTime = $_POST['clocking_time'];
    $clockingType = $_POST['clocking_type'];
    $updatedBy = $_SESSION['user']->getUserId();

    $clockingData = [
        'employee_id' => $employeeId,
        'clocking_date' => $clockingDate,
        'clocking_time' => $clockingTime,
        'clocking_type' => $clockingType,
        'updated_by' => $updatedBy
    ];

    if ($clockingController->addClocking($clockingData)) {
        $auditLogController->logTransaction($updatedBy, 'Add Clocking', 'Clocking data added for employee ID: ' . $employeeId);
        if ($clockingType == 'In') {
            $timeCardController->createTimeCard($employeeId, $clockingDate, $clockingTime);
        } else {
            $timeCardController->updateTimeCard($employeeId, $clockingDate, $clockingTime);
        }
        $successMessage = 'Clocking data added successfully.';
        header('Location: manage_clocking.php?success=add');
        exit();
    } else {
        $errors[] = 'Error adding clocking data.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_clocking'])) {
    $clockingId = $_POST['clocking_id'];
    $employeeId = $_POST['employee_id'];
    $clockingDate = $_POST['clocking_date'];
    $clockingTime = $_POST['clocking_time'];
    $clockingType = $_POST['clocking_type'];
    $updatedBy = $_SESSION['user']->getUserId();

    $clockingData = [
        'employee_id' => $employeeId,
        'clocking_date' => $clockingDate,
        'clocking_time' => $clockingTime,
        'clocking_type' => $clockingType,
        'updated_by' => $updatedBy
    ];

    if ($clockingController->updateClocking($clockingId, $clockingData)) {
        $auditLogController->logTransaction($updatedBy, 'Update Clocking', 'Clocking data updated for clocking ID: ' . $clockingId);
        if ($clockingType == 'In') {
            $timeCardController->createTimeCard($employeeId, $clockingDate, $clockingTime);
        } else {
            $timeCardController->updateTimeCard($employeeId, $clockingDate, $clockingTime);
        }
        $successMessage = 'Clocking data updated successfully.';
        header('Location: manage_clocking.php?success=update');
        exit();
    } else {
        $errors[] = 'Error updating clocking data.';
    }
}

if (isset($_GET['delete'])) {
    $clockingIdToDelete = $_GET['delete'];
    $updatedBy = $_SESSION['user']->getUserId();
    if ($clockingController->deleteClocking($clockingIdToDelete)) {
        $auditLogController->logTransaction($updatedBy, 'Delete Clocking', 'Clocking data deleted for clocking ID: ' . $clockingIdToDelete);
        $successMessage = "Clocking data deleted successfully.";
        header('Location: manage_clocking.php?success=delete');
        exit();
    } else {
        $errors[] = "Error deleting clocking data.";
    }
}

$searchName = isset($_GET['search_name']) ? $_GET['search_name'] : '';
$dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : '';

$clockings = $clockingController->getAllClockings();
$filteredClockings = [];

foreach ($clockings as $clocking) {
    $employee = $employeeController->getEmployeeById($clocking['employee_id']);
    if ($searchName && stripos($employee['employee_name'], $searchName) === false) {
        continue;
    }
    if ($dateFrom && $clocking['clocking_date'] < $dateFrom) {
        continue;
    }
    if ($dateTo && $clocking['clocking_date'] > $dateTo) {
        continue;
    }
    $clocking['employee_name'] = $employee['employee_name'];
    $filteredClockings[] = $clocking;
}

$title = 'Manage Clocking Data';
include __DIR__ . '/../components/header.php';
?>

<div class="container mt-4">
    <!-- <h2 class="mb-4">Manage Clocking Data</h2> -->
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
    <?php elseif (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <?php
            if ($_GET['success'] == 'add') {
                echo 'Clocking data added successfully.';
            } elseif ($_GET['success'] == 'update') {
                echo 'Clocking data updated successfully.';
            } elseif ($_GET['success'] == 'delete') {
                echo 'Clocking data deleted successfully.';
            }
            ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <h2>Clocking List</h2>
            <form method="get" action="manage_clocking.php" class="row g-3 mb-4">
        <div class="col-md-3">
            <input type="text" id="search_name" name="search_name" class="form-control" placeholder="Search by name" value="<?php echo htmlspecialchars($searchName); ?>">
        </div>
        <div class="col-md-3">
            <input type="date" id="date_from" name="date_from" class="form-control" placeholder="Date from" value="<?php echo htmlspecialchars($dateFrom); ?>">
        </div>
        <div class="col-md-3">
            <input type="date" id="date_to" name="date_to" class="form-control" placeholder="Date to" value="<?php echo htmlspecialchars($dateTo); ?>">
        </div>
        <div class="col-md-3 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary me-2">Search</button>
            <button type="button" class="btn btn-secondary" onclick="clearFilters()">Clear</button>
        </div>
    </form>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                foreach ($filteredClockings as $clocking): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($clocking['clocking_id']); ?></td>
                        <td><?php echo htmlspecialchars($clocking['employee_name']); ?></td>
                        <td><?php echo htmlspecialchars($clocking['clocking_date']); ?></td>
                        <td><?php echo htmlspecialchars($clocking['clocking_time']); ?></td>
                        <td><?php echo htmlspecialchars($clocking['clocking_type']); ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" onclick="editClocking(<?php echo $clocking['clocking_id']; ?>)">Edit</button>
                            <a href="?delete=<?php echo $clocking['clocking_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this clocking data?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <h2>Add/Edit Clocking Data</h2>
            <form id="clockingForm" method="post" action="manage_clocking.php">
                <input type="hidden" id="clocking_id" name="clocking_id">
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
                    <label for="clocking_date" class="form-label">Date</label>
                    <input type="date" id="clocking_date" name="clocking_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="clocking_time" class="form-label">Time</label>
                    <input type="time" id="clocking_time" name="clocking_time" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="clocking_type" class="form-label">Type</label>
                    <select id="clocking_type" name="clocking_type" class="form-select" required>
                        <option value="In">In</option>
                        <option value="Out">Out</option>
                        <option value="Break Start">Break Start</option>
                        <option value="Break End">Break End</option>
                    </select>
                </div>
                <button type="submit" name="add_clocking" id="add_clocking_btn" class="btn btn-primary">Add Clocking Data</button>
                <button type="submit" name="update_clocking" id="update_clocking_btn" class="btn btn-primary">Update Clocking Data</button>
            </form>
        </div>
    </div>
</div>



<script>
    document.getElementById('update_clocking_btn').disabled = true;

    function editClocking(clockingId) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var clocking = JSON.parse(xhr.responseText);
                document.getElementById('clocking_id').value = clocking.clocking_id;
                document.getElementById('employee_id').value = clocking.employee_id;
                document.getElementById('clocking_date').value = clocking.clocking_date;
                document.getElementById('clocking_time').value = clocking.clocking_time;
                document.getElementById('clocking_type').value = clocking.clocking_type;

                document.getElementById('add_clocking_btn').disabled = true;
                document.getElementById('update_clocking_btn').disabled = false;
            }
        };
        xhr.open("GET", "get_clocking.php?id=" + clockingId, true);
        xhr.send();
    }

    function clearFilters() {
        document.getElementById('search_name').value = '';
        document.getElementById('date_from').value = '';
        document.getElementById('date_to').value = '';
        document.forms[0].submit();
    }

    document.getElementById('clockingForm').addEventListener('reset', function() {
        document.getElementById('add_clocking_btn').disabled = false;
        document.getElementById('update_clocking_btn').disabled = true;
    });
</script>

<?php include __DIR__ . '/../components/footer.php'; ?>