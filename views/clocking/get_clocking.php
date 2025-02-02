<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../../database/DatabaseHandler.php';
require_once __DIR__ . '/../../controllers/ClockingController.php';

$dbHandler = new DatabaseHandler(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$clockingController = new ClockingController($dbHandler);

if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'views/login.php');
    exit();
}

if (isset($_GET['id'])) {
    $clockingId = $_GET['id'];
    $clocking = $clockingController->getClockingById($clockingId);
    echo json_encode($clocking);
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>