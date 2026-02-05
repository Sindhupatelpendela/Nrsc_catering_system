<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'nrsc_catering');

// App Configuration
define('APP_NAME', 'DIKSTRA - NRSC Service Portal');

// Automatic Base URL Detection
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
// If running on php -S localhost:8000, the path is root. If XAMPP, it might be /nrsc-catering-system/
// We will use a relative path approach for assets to be safe across both.
define('BASE_URL', './'); 

// Error Reporting (Turn off for production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Hide errors from users
ini_set('log_errors', 1);     // Log errors instead
ini_set('error_log', __DIR__ . '/../logs/app.log'); // Ensure logs go to the right file
?>
