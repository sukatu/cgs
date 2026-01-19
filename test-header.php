<?php
// Test file to check if header-main.php works
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Header Test</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Testing Header</h1>
    <?php 
    echo "Before include<br>";
    include 'header-main.php';
    echo "After include<br>";
    ?>
    <p>If you see the navigation above, the header is working.</p>
</body>
</html>
