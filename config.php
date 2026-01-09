<?php
// Start session first, before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'epbvrjuvca');
define('DB_PASS', '86SwzU5UGh');
define('DB_NAME', 'epbvrjuvca');

// Create database connection
function getDBConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        return $conn;
    } catch (Exception $e) {
        die("Database connection error: " . $e->getMessage());
    }
}

// Check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Check if user is logged in
function isUserLoggedIn() {
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

// Require admin login
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: admin-login.php');
        exit();
    }
}

// Require user login
function requireUserLogin() {
    if (!isUserLoggedIn()) {
        header('Location: login-user.html?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit();
    }
}

// Backward compatibility
function isLoggedIn() {
    return isAdminLoggedIn();
}

function requireLogin() {
    requireAdminLogin();
}

// Default admin credentials (change these in production!)
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'cgs2025'); // Change this password!

?>

