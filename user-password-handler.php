<?php
require_once 'config.php';
requireUserLogin();

$conn = getDBConnection();
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validate passwords
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $_SESSION['error'] = 'All password fields are required.';
        header('Location: user-dashboard.php?section=account');
        exit();
    }
    
    if ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = 'New passwords do not match.';
        header('Location: user-dashboard.php?section=account');
        exit();
    }
    
    if (strlen($newPassword) < 8) {
        $_SESSION['error'] = 'New password must be at least 8 characters long.';
        header('Location: user-dashboard.php?section=account');
        exit();
    }
    
    // Get current password from database
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    // Verify current password
    if (!password_verify($currentPassword, $result['password'])) {
        $_SESSION['error'] = 'Current password is incorrect.';
        $conn->close();
        header('Location: user-dashboard.php?section=account');
        exit();
    }
    
    // Update password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $updateStmt->bind_param("si", $hashedPassword, $userId);
    
    if ($updateStmt->execute()) {
        $_SESSION['success'] = 'Password updated successfully!';
    } else {
        $_SESSION['error'] = 'Failed to update password. Please try again.';
    }
    
    $updateStmt->close();
    $conn->close();
    
    header('Location: user-dashboard.php?section=account');
    exit();
} else {
    header('Location: user-dashboard.php?section=account');
    exit();
}
?>
