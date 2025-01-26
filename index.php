<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/autoload.php';

if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'views/login.php'); // Redirect if not logged in
    exit();
}

// Redirect to the home page
header('Location: ' . BASE_URL . 'views/home.php');
exit();
?>