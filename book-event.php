<?php
require_once 'config.php';
requireUserLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = intval($_POST['event_id'] ?? 0);
    $userId = $_SESSION['user_id'];
    $notes = $_POST['notes'] ?? '';
    
    if ($eventId > 0) {
        $conn = getDBConnection();
        
        // Check if already registered
        $checkStmt = $conn->prepare("SELECT id FROM event_registrations WHERE user_id = ? AND event_id = ?");
        $checkStmt->bind_param("ii", $userId, $eventId);
        $checkStmt->execute();
        
        if ($checkStmt->get_result()->num_rows > 0) {
            $_SESSION['booking_error'] = 'You are already registered for this event.';
            header('Location: ' . ($_POST['redirect'] ?? 'events.html'));
            exit();
        }
        
        // Register for event
        $stmt = $conn->prepare("INSERT INTO event_registrations (user_id, event_id, notes, status) VALUES (?, ?, ?, 'confirmed')");
        $stmt->bind_param("iis", $userId, $eventId, $notes);
        
        if ($stmt->execute()) {
            $_SESSION['booking_success'] = 'Successfully registered for the event!';
            header('Location: user-dashboard.php');
            exit();
        } else {
            $_SESSION['booking_error'] = 'Registration failed. Please try again.';
        }
        
        header('Location: ' . ($_POST['redirect'] ?? 'events.html'));
        exit();
    }
}

header('Location: events.html');
exit();
?>
