<?php
// models/User.php (User Class)

require_once __DIR__ . '/../repositories/UserRepository.php';

class User {
    private $userId;
    private $username;
    private $firstName;
    private $lastName;
    private $email;
    private $createdAt;
    private $updatedAt;
    private $updatedBy;
    // ... other properties

    private $userRepository;

    public function __construct($dbHandler, $userId = null, $username = null, $firstName = null, $lastName = null, $email = null, $createdAt = null, $updatedAt = null, $updatedBy = null) {
        $this->userRepository = new UserRepository($dbHandler);
        $this->userId = $userId;
        $this->username = $username;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->updatedBy = $updatedBy;
        // ... initialize other properties if needed
    }

    public function getUserByUsername($username) {
        return $this->userRepository->getUserByUsername($username);
    }

    public function getUserId() { return $this->userId; }
    public function getUsername() { return $this->username; }
    public function getFirstName() { return $this->firstName; }
    public function getLastName() { return $this->lastName; }
    public function getEmail() { return $this->email; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }
    public function getUpdatedBy() { return $this->updatedBy; }
    // ... other getters
}
?>