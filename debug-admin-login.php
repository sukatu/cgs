<?php
// Debug script to check admin login issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Admin Login Debug</h1>";

// Check session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo "<p>Session started: " . (session_status() === PHP_SESSION_ACTIVE ? "Yes" : "No") . "</p>";
echo "<p>Session ID: " . session_id() . "</p>";

// Check config
echo "<h2>Config Check</h2>";
if (file_exists('config.php')) {
    echo "<p>✅ config.php exists</p>";
    require_once 'config.php';
    echo "<p>✅ config.php loaded</p>";
} else {
    echo "<p>❌ config.php NOT FOUND</p>";
    exit;
}

// Check database connection
echo "<h2>Database Check</h2>";
try {
    $conn = getDBConnection();
    echo "<p>✅ Database connection successful</p>";
    
    // Check admin_users table
    $tableCheck = $conn->query("SHOW TABLES LIKE 'admin_users'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        echo "<p>✅ admin_users table exists</p>";
        
        // Check for admin users
        $userCheck = $conn->query("SELECT COUNT(*) as count FROM admin_users");
        if ($userCheck) {
            $row = $userCheck->fetch_assoc();
            echo "<p>Admin users in database: " . $row['count'] . "</p>";
        }
    } else {
        echo "<p>❌ admin_users table does NOT exist</p>";
    }
    
    $conn->close();
} catch (Exception $e) {
    echo "<p>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Check admin-auth.php
echo "<h2>File Check</h2>";
if (file_exists('admin-auth.php')) {
    echo "<p>✅ admin-auth.php exists</p>";
} else {
    echo "<p>❌ admin-auth.php NOT FOUND</p>";
}

if (file_exists('admin-dashboard.php')) {
    echo "<p>✅ admin-dashboard.php exists</p>";
} else {
    echo "<p>❌ admin-dashboard.php NOT FOUND</p>";
}

// Check PHP version
echo "<h2>PHP Info</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Session save path: " . session_save_path() . "</p>";

// Check if logged in
echo "<h2>Login Status</h2>";
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    echo "<p>✅ Currently logged in as: " . htmlspecialchars($_SESSION['admin_username'] ?? 'Unknown') . "</p>";
    echo "<p><a href='admin-dashboard.php'>Go to Dashboard</a></p>";
} else {
    echo "<p>❌ Not logged in</p>";
    echo "<p><a href='admin-login.php'>Go to Login</a></p>";
}
?>
