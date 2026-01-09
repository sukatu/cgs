<?php
require_once 'config.php';
requireUserLogin();

$conn = getDBConnection();
$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

// Create user_library table if it doesn't exist
$createTableSQL = "
CREATE TABLE IF NOT EXISTS `user_library` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `resource_url` varchar(500) DEFAULT NULL,
  `resource_type` varchar(50) DEFAULT NULL,
  `resource_id` varchar(100) DEFAULT NULL,
  `saved_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `resource_type` (`resource_type`),
  UNIQUE KEY `unique_bookmark` (`user_id`, `resource_type`, `resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
$conn->query($createTableSQL);

if ($action === 'remove' && isset($_GET['id'])) {
    $itemId = intval($_GET['id']);
    $itemType = $_GET['type'] ?? '';
    
    // Verify ownership
    $stmt = $conn->prepare("DELETE FROM user_library WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $itemId, $userId);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Item removed from library.';
    } else {
        $_SESSION['error'] = 'Failed to remove item.';
    }
    
    $stmt->close();
    $conn->close();
    
    header('Location: user-dashboard.php?section=library');
    exit();
} else {
    header('Location: user-dashboard.php?section=library');
    exit();
}
?>
