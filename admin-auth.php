<?php
// Start output buffering to prevent headers already sent errors
ob_start();

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $conn = getDBConnection();
    
    // Check database first
    $stmt = $conn->prepare("SELECT id, username, password_hash FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            ob_end_clean();
            header('Location: admin-dashboard.php');
            exit();
        }
    }
    
    // Fallback to config constants (for backward compatibility)
    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        ob_end_clean();
        header('Location: admin-dashboard.php');
        exit();
    }
    
    $_SESSION['login_error'] = 'Invalid username or password';
    ob_end_clean();
    header('Location: admin-login.php');
    exit();
} else {
    ob_end_clean();
    header('Location: admin-login.php');
    exit();
}
?>
