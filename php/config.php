<?php
// php/config.php
// This file contains database connection details and other configurations.

// IMPORTANT: Replace with your actual database credentials
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'stephan'); // e.g., root
define('DB_PASSWORD', 'superuser'); // e.g., '' for no password
define('DB_NAME', 'foodfusion_db');

// Attempt to connect to MySQL database using PDO
try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

// Configuration for account lockout
define('MAX_FAILED_ATTEMPTS', 5); // Number of failed attempts before lockout
define('LOCKOUT_DURATION_MINUTES', 30); // Lockout duration in minutes
?>