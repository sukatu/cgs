<?php
/**
 * Simple test to check if redirects work
 * Access this file to test redirect functionality
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Redirect Test</h1>";

// Test 1: Output buffering
echo "<h2>Test 1: Output Buffering</h2>";
if (ob_get_level() > 0) {
    echo "✓ Output buffering is active (level: " . ob_get_level() . ")<br>";
} else {
    echo "✗ Output buffering is NOT active<br>";
    ob_start();
    echo "→ Started output buffering<br>";
}

// Test 2: Session
echo "<h2>Test 2: Session</h2>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo "✓ Session started (ID: " . session_id() . ")<br>";

// Test 3: Headers
echo "<h2>Test 3: Headers</h2>";
if (!headers_sent()) {
    echo "✓ Headers not yet sent<br>";
} else {
    echo "✗ Headers already sent!<br>";
    echo "Headers sent at: " . (headers_sent($file, $line) ? "$file:$line" : "unknown") . "<br>";
}

// Test 4: Redirect simulation
echo "<h2>Test 4: Redirect Simulation</h2>";
echo "Testing redirect...<br>";
ob_end_clean();
header('Location: test-redirect-success.php');
exit();
