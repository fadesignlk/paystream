<?php
spl_autoload_register(function ($class_name) {
    $directories = [
        'classes/',
        'models/',
        'repositories/',
        'controllers/',
        'database/'
    ];

    foreach ($directories as $directory) {
        $class_path = __DIR__ . '/' . $directory . $class_name . '.php';
        if (file_exists($class_path)) {
            include $class_path;
            return;
        }
    }

    // Handle class not found error. You might want to log this.
    error_log("Class not found: " . $class_name);
    // Optionally throw an exception:
    // throw new Exception("Class not found: " . $class_name);
});

session_start(); // Start the session at the beginning of every file that uses sessions
?>