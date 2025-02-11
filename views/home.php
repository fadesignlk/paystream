<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../database/DatabaseHandler.php';
require_once __DIR__ . '/../models/User.php';

if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'views/login.php'); // Redirect if not logged in
    exit();
}

$dbHandler = new DatabaseHandler(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$userModel = new User($dbHandler);

$user = $_SESSION['user'];
$loggedInUserId = $user->getUserId(); // Get logged in User ID

$title = 'Home';
include __DIR__ . '/components/header.php';
?>

<div class="container mt-4">
    <h1 class="mb-4">Welcome, <?php echo htmlspecialchars($user->getUsername(), ENT_QUOTES); ?>!</h1>
    <div class="row">
        <div class="col-md-3">
            <a href="./employee/add_employee.php" class="card mb-4 text-decoration-none text-dark">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-plus fa-3x"></i>
                    </div>
                    <h5 class="card-title">Add Employee</h5>
                    <p class="card-text">Add a new employee to the system.</p>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="./employee/manage_employee.php" class="card mb-4 text-decoration-none text-dark">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-users-cog fa-3x"></i>
                    </div>
                    <h5 class="card-title">Manage Employees</h5>
                    <p class="card-text">View and manage existing employees.</p>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="./clocking/manage_clocking.php" class="card mb-4 text-decoration-none text-dark">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-clock fa-3x"></i>
                    </div>
                    <h5 class="card-title">Manage Clocking</h5>
                    <p class="card-text">View and manage clocking data.</p>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="./timecard/time_card.php" class="card mb-4 text-decoration-none text-dark">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-calendar-alt fa-3x"></i>
                    </div>
                    <h5 class="card-title">Time Card</h5>
                    <p class="card-text">View and manage time cards.</p>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="./payroll/process_payroll.php" class="card mb-4 text-decoration-none text-dark">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-file-invoice-dollar fa-3x"></i>
                    </div>
                    <h5 class="card-title">Process Payroll</h5>
                    <p class="card-text">Process payroll for all employees.</p>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="./payroll/view_payslips.php" class="card mb-4 text-decoration-none text-dark">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-file-pdf fa-3x"></i>
                    </div>
                    <h5 class="card-title">View Payslips</h5>
                    <p class="card-text">View and download payslips.</p>
                </div>
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/components/footer.php'; ?>