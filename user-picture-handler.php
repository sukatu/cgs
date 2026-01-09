<?php
require_once 'config.php';
requireUserLogin();

$conn = getDBConnection();
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle profile picture upload
    $uploadDir = 'uploads/profile-pictures/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_picture'];
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $fileType = $file['type'];
        if (!in_array($fileType, $allowedTypes)) {
            $_SESSION['error'] = 'Only JPEG, PNG, and GIF images are allowed.';
            header('Location: user-dashboard.php?section=account');
            exit();
        }
        
        // Validate file size (5MB max)
        if ($file['size'] > 5 * 1024 * 1024) {
            $_SESSION['error'] = 'Image size must be less than 5MB.';
            header('Location: user-dashboard.php?section=account');
            exit();
        }
        
        // Generate unique filename
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = $userId . '_' . time() . '.' . $fileExtension;
        $targetPath = $uploadDir . $fileName;
        
        // Delete old profile picture if exists
        $userStmt = $conn->prepare("SELECT profile_picture FROM users WHERE id = ?");
        $userStmt->bind_param("i", $userId);
        $userStmt->execute();
        $result = $userStmt->get_result()->fetch_assoc();
        if ($result && $result['profile_picture'] && file_exists($result['profile_picture'])) {
            unlink($result['profile_picture']);
        }
        $userStmt->close();
        
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Update database
            $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $stmt->bind_param("si", $targetPath, $userId);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = 'Profile picture updated successfully!';
            } else {
                $_SESSION['error'] = 'Failed to update profile picture.';
            }
            
            $stmt->close();
        } else {
            $_SESSION['error'] = 'Failed to upload image.';
        }
    } else {
        $_SESSION['error'] = 'No file uploaded or upload error occurred.';
    }
    
    $conn->close();
    header('Location: user-dashboard.php?section=account');
    exit();
} else {
    header('Location: user-dashboard.php?section=account');
    exit();
}
?>
