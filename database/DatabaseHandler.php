<?php

class DatabaseHandler {
    private $conn;

    public function __construct($host, $username, $password, $dbname) {
        $this->conn = new mysqli($host, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function prepare($query) {
        return $this->conn->prepare($query);
    }

    public function execute($stmt) {
        return $stmt->execute();
    }

    public function getResult($stmt) {
        return $stmt->get_result();
    }

    public function close() {
        $this->conn->close();
    }
}


?>