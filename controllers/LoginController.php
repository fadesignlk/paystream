<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../config.php'; 


class LoginController {
    private $userRepository;

    public function __construct($dbHandler) {
        $this->userRepository = new UserRepository($dbHandler);
    }

    public function login($username, $password) {
        $userData = $this->userRepository->getUserByUsername($username);

        if ($userData && password_verify($password, $userData['password'])) {
            // Successful login
            $user = new User($this->userRepository, $userData['user_id'], $userData['username']);
            $_SESSION['user'] = $user; // Store user object in session
            header('Location: ' . BASE_URL . '/index.php'); // Redirect to index page
            exit();
        } else {
            return 'Invalid username or password.';
        }
    }
}
?>