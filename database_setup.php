<?php
// Database setup script - Run this once to create tables

require_once 'config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists.<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select the database
$conn->select_db(DB_NAME);

// Create events table
$sql = "CREATE TABLE IF NOT EXISTS events (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    event_type ENUM('webinar', 'series', 'other') DEFAULT 'webinar',
    event_date DATETIME NOT NULL,
    location VARCHAR(255),
    format ENUM('online', 'hybrid', 'in-person') DEFAULT 'online',
    registration_link VARCHAR(500),
    youtube_url VARCHAR(500),
    agenda TEXT,
    speakers TEXT,
    moderator VARCHAR(255),
    summary TEXT,
    tags VARCHAR(500),
    countries VARCHAR(255),
    status ENUM('upcoming', 'completed', 'cancelled') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo "Events table created successfully.<br>";
} else {
    echo "Error creating events table: " . $conn->error . "<br>";
}

// Create admin_users table for authentication
$sql = "CREATE TABLE IF NOT EXISTS admin_users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo "Admin users table created successfully.<br>";
    
    // Create default admin user
    $defaultUsername = 'admin';
    $defaultPassword = password_hash('cgs2025', PASSWORD_DEFAULT);
    
    $checkUser = $conn->query("SELECT id FROM admin_users WHERE username = '$defaultUsername'");
    if ($checkUser->num_rows == 0) {
        $insertUser = "INSERT INTO admin_users (username, password_hash, email) 
                       VALUES ('$defaultUsername', '$defaultPassword', 'admin@cgsghana.com')";
        if ($conn->query($insertUser) === TRUE) {
            echo "Default admin user created. Username: admin, Password: cgs2025<br>";
        }
    }
} else {
    echo "Error creating admin_users table: " . $conn->error . "<br>";
}

$conn->close();

echo "<br><strong>Database setup complete!</strong><br>";
echo "<a href='admin-login.php'>Go to Admin Login</a>";
?>
