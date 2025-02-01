<?php

class Logger {
    private $logFile;

    public function __construct($logFile) {
        $this->logFile = $logFile;
    }

    public function log($message) {
        $date = date('Y-m-d H:i:s');
        $formattedMessage = "[$date] $message" . PHP_EOL;
        file_put_contents($this->logFile, $formattedMessage, FILE_APPEND);
    }
}