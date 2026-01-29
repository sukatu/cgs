<?php
/**
 * Handler for online (Zoom) event registrations.
 * Saves to online_zoom_registrations and sends Zoom link via email.
 */

require_once 'config.php';
require_once 'send-zoom-email.php';

ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    header('Location: register-cgs-ii.php');
    exit();
}

$fullName = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$eventId = isset($_POST['event_id']) ? intval($_POST['event_id']) : null;
$eventTitle = trim($_POST['event_title'] ?? 'CGS II Bank Corporate Governance and Financial Stability: The Role of Bank Boards');
$eventDate = trim($_POST['event_date'] ?? 'Thursday, February 12, 2026 at 5:00 PM (Africa/Accra)');
$redirectUrl = $_POST['redirect_url'] ?? 'register-cgs-ii.php';

$errors = [];
if (empty($fullName)) $errors[] = 'Full name is required.';
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email address is required.';

if (!empty($errors)) {
    $_SESSION['registration_error'] = implode(' ', $errors);
    ob_end_clean();
    header('Location: ' . $redirectUrl);
    exit();
}

try {
    $conn = getDBConnection();

    $tableCheck = $conn->query("SHOW TABLES LIKE 'online_zoom_registrations'");
    if (!$tableCheck) {
        throw new Exception("Database error: " . $conn->error);
    }
    if ($tableCheck->num_rows === 0) {
        $sql = "CREATE TABLE IF NOT EXISTS online_zoom_registrations (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            event_id INT(11) DEFAULT NULL,
            event_title VARCHAR(255) DEFAULT NULL,
            full_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(50) DEFAULT NULL,
            registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
            email_sent TINYINT(1) DEFAULT 0,
            zoom_link_sent_at TIMESTAMP NULL,
            notes TEXT,
            INDEX idx_event (event_id),
            INDEX idx_email (email),
            INDEX idx_status (status),
            INDEX idx_registration_date (registration_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        if (!$conn->query($sql)) {
            throw new Exception('Failed to create table: ' . $conn->error);
        }
    }

    $checkStmt = $conn->prepare("SELECT id FROM online_zoom_registrations WHERE email = ? AND (event_id = ? OR (event_id IS NULL AND event_title = ?))");
    $checkStmt->bind_param("sis", $email, $eventId, $eventTitle);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        $checkStmt->close();
        $conn->close();
        $_SESSION['registration_error'] = 'You have already registered for this event with this email address.';
        ob_end_clean();
        header('Location: ' . $redirectUrl);
        exit();
    }
    $checkStmt->close();

    $stmt = $conn->prepare("INSERT INTO online_zoom_registrations (event_id, event_title, full_name, email, phone, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    $phoneVal = $phone !== '' ? $phone : '';
    $stmt->bind_param("issss", $eventId, $eventTitle, $fullName, $email, $phoneVal);
    if (!$stmt->execute()) {
        throw new Exception('Insert failed: ' . $conn->error);
    }
    $lastId = (int) $conn->insert_id;
    $stmt->close();

    $result = ['success' => false, 'message' => ''];
    try {
        $result = sendZoomLinkEmail($email, $fullName, $eventTitle, $eventDate);
    } catch (Throwable $e) {
        error_log('Zoom email send error: ' . $e->getMessage());
    }
    $emailSent = $result['success'] ? 1 : 0;
    $zoomSentAt = $result['success'] ? date('Y-m-d H:i:s') : null;
    if ($lastId) {
        $up = $conn->prepare("UPDATE online_zoom_registrations SET email_sent = ?, zoom_link_sent_at = ? WHERE id = ?");
        $up->bind_param("isi", $emailSent, $zoomSentAt, $lastId);
        $up->execute();
        $up->close();
    }

    $conn->close();

    if ($result['success']) {
        $_SESSION['registration_success'] = 'Thank you! Your registration has been saved. We have sent the Zoom meeting link to your email (' . htmlspecialchars($email) . '). Please check your inbox (and spam folder).';
    } else {
        $_SESSION['registration_success'] = 'Thank you! Your registration has been saved. We could not send the Zoom link by email; please use the link on this page to join: Join Zoom Meeting (Meeting ID: 885 0243 0789, Passcode: 822412).';
    }
} catch (Exception $e) {
    error_log('Online registration error: ' . $e->getMessage());
    $_SESSION['registration_error'] = 'Registration failed. Please try again or contact us.';
    $_SESSION['registration_error_detail'] = $e->getMessage();
}

ob_end_clean();
header('Location: ' . $redirectUrl);
exit();
