<?php
/**
 * Test Paper Submission Database
 * Run this to verify paper submission database functionality
 * Access at: http://localhost/cgs/test-paper-submission.php
 */

require_once 'config.php';
session_start();

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paper Submission Database Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
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
        .warning {
            background: #fff3e0;
            color: #f57c00;
            border: 1px solid #ffcc02;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            background: white;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #1976d2;
            color: white;
        }
    </style>
</head>
<body>
    <h1>Paper Submission Database Test</h1>
    
    <?php
    // Test 1: Database Connection
    echo "<h2>Test 1: Database Connection</h2>";
    try {
        $conn = getDBConnection();
        echo "<div class='test-result success'>✅ Database connection successful!</div>";
    } catch (Exception $e) {
        echo "<div class='test-result error'>❌ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</div>";
        exit();
    }
    
    // Test 2: Check if user_papers table exists
    echo "<h2>Test 2: user_papers Table</h2>";
    $tableCheck = $conn->query("SHOW TABLES LIKE 'user_papers'");
    if ($tableCheck->num_rows > 0) {
        echo "<div class='test-result success'>✅ user_papers table exists</div>";
        
        // Get table structure
        $structure = $conn->query("DESCRIBE user_papers");
        echo "<div class='test-result info'><strong>Table Structure:</strong>";
        echo "<table><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = $structure->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table></div>";
        
    } else {
        echo "<div class='test-result error'>❌ user_papers table does not exist!</div>";
        echo "<div class='test-result warning'>Run create-all-tables.php or create-user-tables.php to create the table.</div>";
    }
    
    // Test 3: Test INSERT query preparation
    echo "<h2>Test 3: INSERT Query Preparation</h2>";
    $testStmt = $conn->prepare("
        INSERT INTO user_papers (user_id, title, abstract, keywords, category, file_path, status, submitted_date)
        VALUES (?, ?, ?, ?, ?, ?, 'under-review', NOW())
    ");
    if ($testStmt) {
        echo "<div class='test-result success'>✅ INSERT query preparation successful</div>";
        $testStmt->close();
    } else {
        echo "<div class='test-result error'>❌ INSERT query preparation failed: " . $conn->error . "</div>";
    }
    
    // Test 4: Check existing papers
    echo "<h2>Test 4: Existing Papers</h2>";
    $papersQuery = $conn->query("SELECT COUNT(*) as count FROM user_papers");
    if ($papersQuery) {
        $count = $papersQuery->fetch_assoc()['count'];
        echo "<div class='test-result info'>Total papers in database: <strong>$count</strong></div>";
        
        if ($count > 0) {
            $papers = $conn->query("SELECT id, user_id, title, status, submitted_date, file_path FROM user_papers ORDER BY submitted_date DESC LIMIT 10");
            echo "<div class='test-result info'><strong>Recent Papers:</strong>";
            echo "<table><tr><th>ID</th><th>User ID</th><th>Title</th><th>Status</th><th>Submitted</th><th>File Path</th></tr>";
            while ($paper = $papers->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($paper['id']) . "</td>";
                echo "<td>" . htmlspecialchars($paper['user_id']) . "</td>";
                echo "<td>" . htmlspecialchars(substr($paper['title'], 0, 40)) . "...</td>";
                echo "<td>" . htmlspecialchars($paper['status']) . "</td>";
                echo "<td>" . htmlspecialchars($paper['submitted_date']) . "</td>";
                echo "<td>" . htmlspecialchars(substr($paper['file_path'], 0, 30)) . "...</td>";
                echo "</tr>";
            }
            echo "</table></div>";
        }
    } else {
        echo "<div class='test-result error'>❌ Could not query papers: " . $conn->error . "</div>";
    }
    
    // Test 5: Check uploads directory
    echo "<h2>Test 5: Uploads Directory</h2>";
    $uploadDir = 'uploads/papers/';
    if (file_exists($uploadDir)) {
        echo "<div class='test-result success'>✅ Upload directory exists: $uploadDir</div>";
        if (is_writable($uploadDir)) {
            echo "<div class='test-result success'>✅ Upload directory is writable</div>";
        } else {
            echo "<div class='test-result error'>❌ Upload directory is NOT writable. Please set permissions.</div>";
        }
    } else {
        echo "<div class='test-result warning'>⚠️ Upload directory does not exist: $uploadDir</div>";
        echo "<div class='test-result info'>The directory will be created automatically when you submit a paper.</div>";
    }
    
    // Test 6: Test with sample data (dry run)
    echo "<h2>Test 6: Sample Insert Test (Dry Run)</h2>";
    if (isset($_SESSION['user_id'])) {
        $testUserId = $_SESSION['user_id'];
        $testTitle = "Test Paper " . time();
        $testAbstract = "This is a test abstract";
        $testKeywords = "test, paper";
        $testCategory = "research";
        $testFilePath = "uploads/papers/test.pdf";
        
        $testStmt = $conn->prepare("
            INSERT INTO user_papers (user_id, title, abstract, keywords, category, file_path, status, submitted_date)
            VALUES (?, ?, ?, ?, ?, ?, 'under-review', NOW())
        ");
        
        if ($testStmt) {
            $testStmt->bind_param("isssss", $testUserId, $testTitle, $testAbstract, $testKeywords, $testCategory, $testFilePath);
            // Don't actually execute - just test binding
            echo "<div class='test-result success'>✅ Sample data binding successful</div>";
            echo "<div class='test-result info'>User ID: $testUserId, Title: $testTitle</div>";
            $testStmt->close();
        } else {
            echo "<div class='test-result error'>❌ Sample data binding failed: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='test-result warning'>⚠️ Not logged in. Cannot test with actual user ID.</div>";
    }
    
    $conn->close();
    ?>
    
    <div style="margin-top: 30px; padding: 20px; background: #fff; border-radius: 4px;">
        <h3>Next Steps:</h3>
        <ul>
            <li>If all tests pass, paper submission should work</li>
            <li>If table is missing, run: <a href="create-all-tables.php">create-all-tables.php</a></li>
            <li>If upload directory is not writable, set permissions: <code>chmod 755 uploads/papers/</code></li>
            <li>Test paper submission at: <a href="user-dashboard.php?section=papers">User Dashboard - Papers</a></li>
            <li>Check PHP error logs if submission fails</li>
        </ul>
    </div>
</body>
</html>
