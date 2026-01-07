<?php
require_once 'config.php';

header('Content-Type: application/json');

$conn = getDBConnection();

// Fetch all active images
$query = "SELECT filename, alt_text, category FROM gallery_images WHERE is_active = 1 ORDER BY display_order ASC, upload_date DESC";
$result = $conn->query($query);

$images = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $images[] = [
            'filename' => $row['filename'],
            'path' => 'images/uploads/' . $row['filename'],
            'alt' => $row['alt_text'] ?: 'CGS Gallery Image',
            'category' => $row['category']
        ];
    }
}

$conn->close();

echo json_encode([
    'success' => true,
    'images' => $images,
    'count' => count($images)
]);
?>

