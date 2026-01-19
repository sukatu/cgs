<?php
/**
 * Database Connection Test Script
 * Run this to verify database connectivity and table structure
 * Access at: http://localhost/cgs/test-db-connection.php
 */

require_once 'config.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-result {
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success {
            background: #e8f5e9;
            color: #388e3c;
            border: 1px solid #c8e6c9;
        }
        .error {
            background: #ffebee;
            color: #d32f2f;
            border: 1px solid #ffcdd2;
        }
        .info {
            background: #e3f2fd;
            color: #1976d2;
            border: 1px solid #90caf9;
        }
        h1 {
            color: #1976d2;
        }
        pre {
            background: #fff;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>Database Connection Test</h1>
    
    <?php
    // Test 1: Database Connection
    echo "<h2>Test 1: Database Connection</h2>";
    try {
        $conn = getDBConnection();
        echo "<div class='test-result success'>✅ Database connection successful!</div>";
        echo "<div class='test-result info'>Host: " . DB_HOST . "<br>Database: " . DB_NAME . "</div>";
    } catch (Exception $e) {
        echo "<div class='test-result error'>❌ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</div>";
        exit();
    }
    
    // Test 2: Check if users table exists
    echo "<h2>Test 2: Users Table</h2>";
    $tableCheck = $conn->query("SHOW TABLES LIKE 'users'");
    if ($tableCheck->num_rows > 0) {
        echo "<div class='test-result success'>✅ Users table exists</div>";
        
        // Get table structure
        $structure = $conn->query("DESCRIBE users");
        echo "<div class='test-result info'><strong>Table Structure:</strong><pre>";
        echo "Column Name | Type | Null | Key | Default\n";
        echo str_repeat("-", 60) . "\n";
        while ($row = $structure->fetch_assoc()) {
            echo sprintf("%-15s | %-20s | %-5s | %-5s | %s\n", 
                $row['Field'], 
                $row['Type'], 
                $row['Null'], 
                $row['Key'], 
                $row['Default'] ?? 'NULL'
            );
        }
        echo "</pre></div>";
        
        // Count users
        $userCount = $conn->query("SELECT COUNT(*) as count FROM users");
        $count = $userCount->fetch_assoc()['count'];
        echo "<div class='test-result info'>Total users in database: <strong>$count</strong></div>";
        
    } else {
        echo "<div class='test-result error'>❌ Users table does not exist. Please run create-all-tables.php</div>";
    }
    
    // Test 3: Test user creation (dry run)
    echo "<h2>Test 3: Registration Query Test</h2>";
    $testEmail = "test_" . time() . "@example.com";
    $testStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if ($testStmt) {
        $testStmt->bind_param("s", $testEmail);
        $testStmt->execute();
        echo "<div class='test-result success'>✅ Registration query preparation successful</div>";
        $testStmt->close();
    } else {
        echo "<div class='test-result error'>❌ Registration query preparation failed: " . $conn->error . "</div>";
    }
    
    // Test 4: Test login query
    echo "<h2>Test 4: Login Query Test</h2>";
    $loginStmt = $conn->prepare("SELECT id, name, email, password_hash FROM users WHERE email = ?");
    if ($loginStmt) {
        echo "<div class='test-result success'>✅ Login query preparation successful</div>";
        $loginStmt->close();
    } else {
        echo "<div class='test-result error'>❌ Login query preparation failed: " . $conn->error . "</div>";
    }
    
    // Test 5: Check password hashing
    echo "<h2>Test 5: Password Hashing</h2>";
    $testPassword = "testpassword123";
    $hash = password_hash($testPassword, PASSWORD_DEFAULT);
    if ($hash && password_verify($testPassword, $hash)) {
        echo "<div class='test-result success'>✅ Password hashing and verification working correctly</div>";
    } else {
        echo "<div class='test-result error'>❌ Password hashing failed</div>";
    }
    
    // Test 6: Sample user data (if any exists)
    echo "<h2>Test 6: Sample User Data</h2>";
    $sampleUsers = $conn->query("SELECT id, name, email, country, city, role, created_at FROM users ORDER BY created_at DESC LIMIT 5");
    if ($sampleUsers->num_rows > 0) {
        echo "<div class='test-result info'><strong>Recent Users:</strong><pre>";
        echo "ID | Name | Email | Country | City | Role | Created\n";
        echo str_repeat("-", 80) . "\n";
        while ($user = $sampleUsers->fetch_assoc()) {
            echo sprintf("%-3d | %-20s | %-25s | %-15s | %-15s | %-15s | %s\n",
                $user['id'],
                substr($user['name'], 0, 20),
                substr($user['email'], 0, 25),
                substr($user['country'] ?? 'N/A', 0, 15),
                substr($user['city'] ?? 'N/A', 0, 15),
                substr($user['role'] ?? 'N/A', 0, 15),
                $user['created_at']
            );
        }
        echo "</pre></div>";
    } else {
        echo "<div class='test-result info'>No users found in database. Registration will create the first user.</div>";
    }
    
    $conn->close();
    ?>
    
    <div style="margin-top: 30px; padding: 20px; background: #fff; border-radius: 4px;">
        <h3>Next Steps:</h3>
        <ul>
            <li>If all tests pass, your database is ready for login and registration</li>
            <li>If users table is missing, run: <a href="create-all-tables.php">create-all-tables.php</a></li>
            <li>Test registration at: <a href="network.php">network.php</a></li>
            <li>Test login at: <a href="login-user.php">login-user.php</a></li>
        </ul>
    </div>
</body>
</html>
