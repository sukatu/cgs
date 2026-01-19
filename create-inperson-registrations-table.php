<?php
/**
 * Script to create in-person registrations table
 * Run this once: http://yourdomain.com/create-inperson-registrations-table.php
 */

require_once 'config.php';

$conn = getDBConnection();

// Create in_person_registrations table
$sql = "CREATE TABLE IF NOT EXISTS in_person_registrations (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    event_id INT(11) DEFAULT NULL,
    event_title VARCHAR(255) DEFAULT NULL,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    address TEXT NOT NULL,
    institution_firm VARCHAR(255) NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    INDEX idx_event (event_id),
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_registration_date (registration_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo "✅ In-person registrations table created successfully!<br>";
    echo "The table is ready to store registrations for CGS II and other events.<br>";
} else {
    echo "❌ Error creating table: " . $conn->error . "<br>";
}

$conn->close();
?>
