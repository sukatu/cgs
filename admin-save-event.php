<?php
require_once 'config.php';
requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    
    $event_id = isset($_POST['event_id']) && !empty($_POST['event_id']) ? intval($_POST['event_id']) : null;
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $event_type = $_POST['event_type'] ?? 'webinar';
    $event_date = $_POST['event_date'] ?? '';
    $location = $_POST['location'] ?? '';
    $format = $_POST['format'] ?? 'online';
    $registration_link = $_POST['registration_link'] ?? '';
    $youtube_url = $_POST['youtube_url'] ?? '';
    $speakers = $_POST['speakers'] ?? '';
    $moderator = $_POST['moderator'] ?? '';
    $agenda = $_POST['agenda'] ?? '';
    $summary = $_POST['summary'] ?? '';
    $tags = $_POST['tags'] ?? '';
    $countries = $_POST['countries'] ?? '';
    $status = $_POST['status'] ?? 'upcoming';
    
    // Convert datetime-local format to MySQL datetime
    if (!empty($event_date)) {
        $event_date = date('Y-m-d H:i:s', strtotime($event_date));
    }
    
    if ($event_id) {
        // Update existing event
        $stmt = $conn->prepare("UPDATE events SET 
            title = ?, description = ?, event_type = ?, event_date = ?, location = ?, 
            format = ?, registration_link = ?, youtube_url = ?, speakers = ?, moderator = ?, 
            agenda = ?, summary = ?, tags = ?, countries = ?, status = ?
            WHERE id = ?");
        
        $stmt->bind_param("sssssssssssssssi",
            $title, $description, $event_type, $event_date, $location,
            $format, $registration_link, $youtube_url, $speakers, $moderator,
            $agenda, $summary, $tags, $countries, $status, $event_id
        );
    } else {
        // Insert new event
        $stmt = $conn->prepare("INSERT INTO events 
            (title, description, event_type, event_date, location, format, 
             registration_link, youtube_url, speakers, moderator, agenda, 
             summary, tags, countries, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("sssssssssssssss",
            $title, $description, $event_type, $event_date, $location,
            $format, $registration_link, $youtube_url, $speakers, $moderator,
            $agenda, $summary, $tags, $countries, $status
        );
    }
    
    if ($stmt->execute()) {
        header('Location: admin-dashboard.php?success=1');
        exit();
    } else {
        header('Location: admin-dashboard.php?error=' . urlencode($stmt->error));
        exit();
    }
    
    $stmt->close();
    $conn->close();
} else {
    header('Location: admin-dashboard.php');
    exit();
}
?>
