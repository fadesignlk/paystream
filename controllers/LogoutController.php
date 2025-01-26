<?php

require_once __DIR__ . '/../config.php'; // Include the config file to access BASE_URL

class LogoutController {
    public function logout() {
        // Destroy the session
        session_start();
        session_unset();
        session_destroy();

        // Redirect to the login page
        header('Location: ' . BASE_URL . 'views/login.php');
        exit();
    }
}
?>