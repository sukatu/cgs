<?php
/**
 * Debug script to test authentication flow
 * Access this file directly to see any errors
 */

// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h1>Authentication Debug</h1>";

// Test session
echo "<h2>Session Test</h2>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo "Session ID: " . session_id() . "<br>";
echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? "Active" : "Inactive") . "<br>";

// Test config
echo "<h2>Config Test</h2>";
try {
    require_once 'config.php';
    echo "Config loaded successfully<br>";
} catch (Exception $e) {
    echo "Config error: " . $e->getMessage() . "<br>";
}

// Test database connection
echo "<h2>Database Test</h2>";
try {
    $conn = getDBConnection();
    echo "Database connected successfully<br>";
    
    // Check if users table exists
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows > 0) {
        echo "Users table exists<br>";
        
        // Check user count
        $countResult = $conn->query("SELECT COUNT(*) as count FROM users");
        $count = $countResult->fetch_assoc()['count'];
        echo "Total users: " . $count . "<br>";
    } else {
        echo "Users table does NOT exist<br>";
    }
    
    $conn->close();
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "<br>";
}

// Test user-auth.php
echo "<h2>User Auth File Test</h2>";
if (file_exists('user-auth.php')) {
    echo "user-auth.php exists<br>";
    echo "File size: " . filesize('user-auth.php') . " bytes<br>";
    
    // Check for syntax errors
    $output = [];
    $return_var = 0;
    exec("php -l user-auth.php 2>&1", $output, $return_var);
    if ($return_var === 0) {
        echo "No syntax errors in user-auth.php<br>";
    } else {
        echo "Syntax errors found:<br>";
        echo "<pre>" . implode("\n", $output) . "</pre>";
    }
} else {
    echo "user-auth.php does NOT exist<br>";
}

// Test user-dashboard.php
echo "<h2>User Dashboard File Test</h2>";
if (file_exists('user-dashboard.php')) {
    echo "user-dashboard.php exists<br>";
    echo "File size: " . filesize('user-dashboard.php') . " bytes<br>";
    
    // Check for syntax errors
    $output = [];
    $return_var = 0;
    exec("php -l user-dashboard.php 2>&1", $output, $return_var);
    if ($return_var === 0) {
        echo "No syntax errors in user-dashboard.php<br>";
    } else {
        echo "Syntax errors found:<br>";
        echo "<pre>" . implode("\n", $output) . "</pre>";
    }
} else {
    echo "user-dashboard.php does NOT exist<br>";
}

// Check PHP version
echo "<h2>PHP Info</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Session save path: " . session_save_path() . "<br>";
echo "Session save path writable: " . (is_writable(session_save_path()) ? "Yes" : "No") . "<br>";

// Check for output before headers
echo "<h2>Output Buffer Test</h2>";
if (ob_get_level() > 0) {
    echo "Output buffering is active<br>";
    echo "Buffer level: " . ob_get_level() . "<br>";
} else {
    echo "Output buffering is NOT active<br>";
}

echo "<hr>";
echo "<p><strong>Note:</strong> Delete this file after debugging for security.</p>";
?>
