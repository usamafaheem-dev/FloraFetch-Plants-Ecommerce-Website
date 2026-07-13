<?php
// Start session for login and cart functionality across all pages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database credentials
$host = "localhost";
$username = "root";       // Default XAMPP username
$password = "";           // Default XAMPP password is empty
$database = "florafetch_db";

// Temporarily suppress warnings and exceptions in case database is not imported yet
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
mysqli_report(MYSQLI_REPORT_OFF); // Prevents PHP 8.1+ from throwing fatal exceptions for DB connection

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Instead of die(), we just let it fail silently for now so you can see the UI (demo data will show)
// if (!$conn) {
//     die("Connection failed: " . mysqli_connect_error());
// }

// Function to safely sanitize inputs
if (!function_exists('sanitize_input')) {
    function sanitize_input($conn, $data) {
        if (!$conn) return htmlspecialchars(trim($data));
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return mysqli_real_escape_string($conn, $data);
    }
}
?>
