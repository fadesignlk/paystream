<?php
// models/User.php (User Class)

require_once __DIR__ . '/../repositories/UserRepository.php';

class User {
    private $userId;
    private $username;
    // ... other properties

    private $userRepository;

    public function __construct($dbHandler, $userId = null, $username = null) {
        $this->userRepository = new UserRepository($dbHandler);
        $this->userId = $userId;
        $this->username = $username;
        // ... initialize other properties if needed
    }

    public function getUserByUsername($username) {
        return $this->userRepository->getUserByUsername($username);
    }

    public function getUserId() { return $this->userId; }
    public function getUsername() { return $this->username; }
    // ... other getters
}
?>