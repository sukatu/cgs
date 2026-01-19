<?php
require_once 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
    $_SESSION['error'] = 'Please log in to add items to your library.';
    $redirectUrl = $_GET['redirect'] ?? 'blog.php';
    header('Location: login-user.php?redirect=' . urlencode($redirectUrl));
    exit();
}

$conn = getDBConnection();
$userId = $_SESSION['user_id'];

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

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$resourceType = $_GET['type'] ?? $_POST['type'] ?? '';
$resourceId = $_GET['id'] ?? $_POST['id'] ?? '';
$title = $_POST['title'] ?? $_GET['title'] ?? '';
$description = $_POST['description'] ?? $_GET['description'] ?? '';
$resourceUrl = $_POST['url'] ?? $_GET['url'] ?? '';

if ($action === 'add' && $resourceType && $resourceId) {
    // Check if already bookmarked
    $checkStmt = $conn->prepare("SELECT id FROM user_library WHERE user_id = ? AND resource_type = ? AND resource_id = ?");
    $checkStmt->bind_param("iss", $userId, $resourceType, $resourceId);
    $checkStmt->execute();
    
    if ($checkStmt->get_result()->num_rows > 0) {
        $_SESSION['error'] = 'This item is already in your library.';
        $checkStmt->close();
        $conn->close();
        
        // Redirect back to source page
        $redirectUrl = $_GET['redirect'] ?? ($resourceType === 'article' ? 'blog.php' : 'videos.php');
        header('Location: ' . $redirectUrl);
        exit();
    }
    $checkStmt->close();
    
    // Add to library
    $stmt = $conn->prepare("
        INSERT INTO user_library (user_id, title, description, resource_url, resource_type, resource_id, saved_date)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("isssss", $userId, $title, $description, $resourceUrl, $resourceType, $resourceId);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Item added to your library!';
    } else {
        $_SESSION['error'] = 'Failed to add item to library.';
    }
    
    $stmt->close();
    $conn->close();
    
    // Redirect back to source page
    $redirectUrl = $_GET['redirect'] ?? ($resourceType === 'article' ? 'blog.php' : 'videos.php');
    header('Location: ' . $redirectUrl);
    exit();
    
} elseif ($action === 'remove') {
    $itemId = intval($_GET['id'] ?? $_POST['id'] ?? 0);
    $itemType = $_GET['type'] ?? $_POST['type'] ?? '';
    
    if ($itemId > 0) {
        // Verify ownership
        $stmt = $conn->prepare("DELETE FROM user_library WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $itemId, $userId);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Item removed from library.';
        } else {
            $_SESSION['error'] = 'Failed to remove item.';
        }
        
        $stmt->close();
    }
    
    $conn->close();
    header('Location: user-dashboard.php?section=library');
    exit();
} else {
    header('Location: user-dashboard.php?section=library');
    exit();
}
?>
