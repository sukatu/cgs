<?php
require_once 'config.php';
requireUserLogin();

$conn = getDBConnection();
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $organization = $_POST['organization'] ?? '';
    $profession = $_POST['profession'] ?? $_POST['role'] ?? ''; // Support both profession and role for compatibility
    $role = $profession; // Map profession to role for database (database column is still 'role')
    $city = $_POST['city'] ?? '';
    $country = $_POST['country'] ?? '';
    $bio = $_POST['bio'] ?? '';
    
    // Check if email is already taken by another user
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $checkStmt->bind_param("si", $email, $userId);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        $_SESSION['error'] = 'Email address is already in use.';
        $checkStmt->close();
        $conn->close();
        header('Location: user-dashboard.php?section=account');
        exit();
    }
    $checkStmt->close();
    
    // Update user account
    $stmt = $conn->prepare("
        UPDATE users 
        SET name = ?, email = ?, organization = ?, role = ?, city = ?, country = ?, bio = ?
        WHERE id = ?
    ");
    $stmt->bind_param("sssssssi", $name, $email, $organization, $role, $city, $country, $bio, $userId);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Account updated successfully!';
        $_SESSION['user_name'] = $name; // Update session name
    } else {
        $_SESSION['error'] = 'Failed to update account. Please try again.';
    }
    
    $stmt->close();
    $conn->close();
    
    header('Location: user-dashboard.php?section=account');
    exit();
} else {
    header('Location: user-dashboard.php?section=account');
    exit();
}
?>
