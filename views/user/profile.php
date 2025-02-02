<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../../database/DatabaseHandler.php';
require_once __DIR__ . '/../../controllers/UserController.php';


if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'views/login.php');
    exit();
}

$user = $_SESSION['user'];
$loggedInUserId = $user->getUserId();

$dbHandler = new DatabaseHandler(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$userController = new UserController($dbHandler);
$userData = $userController->getUserById($loggedInUserId);

$title = 'User Profile';
include __DIR__ . '../../components/header.php';
?>

<div class="container mt-4">
    <h1 class="mb-4">User Profile</h1>
    <div class="row">
        <div class="col-md-6">
            <?php if ($userData): ?>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($userData['first_name']) . ' ' . htmlspecialchars($userData['last_name']); ?></h5>
                    <p class="card-text"><strong>Email:</strong> <?php echo htmlspecialchars($userData['email']); ?></p>
                    <!-- Add more fields as necessary -->
                </div>
            </div>
            <?php else: ?>
                <div class="alert alert-danger">User not found.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '../../components/footer.php'; ?>