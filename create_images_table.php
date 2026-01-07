<?php
require_once 'config.php';

$conn = getDBConnection();

// Create gallery_images table
$sql = "CREATE TABLE IF NOT EXISTS gallery_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    alt_text VARCHAR(255) DEFAULT NULL,
    category VARCHAR(100) DEFAULT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'gallery_images' created successfully!<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Create uploads directory if it doesn't exist
$uploadDir = 'images/uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
    echo "Upload directory created: $uploadDir<br>";
}

$conn->close();
echo "Setup complete!";
?>

