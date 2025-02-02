<?php

require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../utils/Logger.php';

class UserController {
    private $userRepository;
    private $logger;

    public function __construct($dbHandler) {
        $this->userRepository = new UserRepository($dbHandler);
        $this->logger = new Logger(__DIR__ . '/../logs/user_controller.log');
    }

    public function getUserById($userId) {
        $this->logger->log("Fetching user by ID: $userId");
        return $this->userRepository->findById($userId);
    }
}