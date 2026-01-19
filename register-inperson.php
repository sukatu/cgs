<?php
/**
 * Handler for in-person event registrations
 * Processes form submissions from the registration form
 */

require_once 'config.php';

// Start output buffering
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $institutionFirm = trim($_POST['institution_firm'] ?? '');
    $eventId = isset($_POST['event_id']) ? intval($_POST['event_id']) : null;
    $eventTitle = trim($_POST['event_title'] ?? 'CGS II Bank Corporate Governance and Financial Stability: The Role of Bank Boards');
    $redirectUrl = $_POST['redirect_url'] ?? 'index.php';
    
    // Validation
    $errors = [];
    
    if (empty($fullName)) {
        $errors[] = 'Full name is required.';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email address is required.';
    }
    
    if (empty($phone)) {
        $errors[] = 'Phone number is required.';
    }
    
    if (empty($address)) {
        $errors[] = 'Address is required.';
    }
    
    if (empty($institutionFirm)) {
        $errors[] = 'Institution/Firm is required.';
    }
    
    if (empty($errors)) {
        try {
            $conn = getDBConnection();
            
            // Check if table exists, create if not
            $tableCheck = $conn->query("SHOW TABLES LIKE 'in_person_registrations'");
            if ($tableCheck->num_rows === 0) {
                $createTableSQL = "CREATE TABLE IF NOT EXISTS in_person_registrations (
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
                
                if (!$conn->query($createTableSQL)) {
                    throw new Exception("Failed to create table: " . $conn->error);
                }
            }
            
            // Check for duplicate registration (same email and event)
            if ($eventId) {
                $checkStmt = $conn->prepare("SELECT id FROM in_person_registrations WHERE email = ? AND event_id = ?");
                $checkStmt->bind_param("si", $email, $eventId);
            } else {
                $checkStmt = $conn->prepare("SELECT id FROM in_person_registrations WHERE email = ? AND event_title = ?");
                $checkStmt->bind_param("ss", $email, $eventTitle);
            }
            $checkStmt->execute();
            
            if ($checkStmt->get_result()->num_rows > 0) {
                $_SESSION['registration_error'] = 'You have already registered for this event with this email address.';
            } else {
                // Insert registration
                $stmt = $conn->prepare("INSERT INTO in_person_registrations (event_id, event_title, full_name, email, phone, address, institution_firm, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
                $stmt->bind_param("issssss", $eventId, $eventTitle, $fullName, $email, $phone, $address, $institutionFirm);
                
                if ($stmt->execute()) {
                    $_SESSION['registration_success'] = 'Thank you! Your registration has been submitted successfully. We will contact you with further details.';
                } else {
                    throw new Exception("Registration failed: " . $stmt->error);
                }
                
                $stmt->close();
            }
            
            $checkStmt->close();
            $conn->close();
            
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            $_SESSION['registration_error'] = 'Registration failed. Please try again or contact us directly.';
        }
    } else {
        $_SESSION['registration_error'] = implode(' ', $errors);
    }
    
    // Redirect back
    ob_end_clean();
    header('Location: ' . $redirectUrl . '#cgsIIModal');
    exit();
}

// If not POST, redirect to home
ob_end_clean();
header('Location: index.php');
exit();
?>
