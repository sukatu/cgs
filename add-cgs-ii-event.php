<?php
/**
 * Script to add CGS II event to the database
 * Run this once: http://yourdomain.com/add-cgs-ii-event.php
 */

require_once 'config.php';

$conn = getDBConnection();

// Check if event already exists
$checkStmt = $conn->prepare("SELECT id FROM events WHERE title LIKE '%CGS II%' AND event_date = ?");
$eventDate = '2026-02-12 17:00:00'; // February 12, 2026 at 5:00 PM (17:00) GMT
$checkStmt->bind_param("s", $eventDate);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    echo "❌ Event already exists in the database.<br>";
    $checkStmt->close();
    $conn->close();
    exit();
}

// Prepare event data
$title = "CGS II Bank Corporate Governance and Financial Stability: The Role of Bank Boards";
$description = "Join us for CGS II, a critical discussion on bank corporate governance and financial stability, focusing on the essential role of bank boards in ensuring robust governance frameworks and financial stability in the banking sector.";
$eventType = 'series';
$eventDate = '2026-02-12 17:00:00'; // February 12, 2026 at 5:00 PM (17:00) GMT (Africa/Accra timezone)
$location = "Dr. Daniel McKorley Moot Court Room, GIMPA Law School";
$format = 'hybrid';
$registrationLink = "https://us06web.zoom.us/j/88502430789?pwd=e3a79VijbjKZTolGnhZDoaN4s7OIug.1";
$status = 'upcoming';

// Additional Zoom details for description
$zoomDetails = "\n\nZoom Meeting Details:\n";
$zoomDetails .= "Meeting ID: 885 0243 0789\n";
$zoomDetails .= "Passcode: 822412\n";
$zoomDetails .= "\nJoin instructions: https://us06web.zoom.us/meetings/88502430789/invitations?signature=jv3kLZCqPxnGY0kOXjKJ-j_yX8d2Rbww5hhLcVJeOWA";

$fullDescription = $description . $zoomDetails;

// Insert the event
$stmt = $conn->prepare("INSERT INTO events (title, description, event_type, event_date, location, format, registration_link, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssss", $title, $fullDescription, $eventType, $eventDate, $location, $format, $registrationLink, $status);

if ($stmt->execute()) {
    $eventId = $conn->insert_id;
    echo "✅ CGS II event successfully added to the database!<br>";
    echo "Event ID: " . $eventId . "<br>";
    echo "Title: " . htmlspecialchars($title) . "<br>";
    echo "Date: February 12, 2026 at 5:00 PM (Africa/Accra)<br>";
    echo "Registration Link: " . htmlspecialchars($registrationLink) . "<br><br>";
    echo "The event will now appear on:<br>";
    echo "- Series Diary page (series-diary.php)<br>";
    echo "- Events page (events.php)<br>";
    echo "- Homepage (if configured to show upcoming events)<br>";
} else {
    echo "❌ Error adding event: " . $stmt->error . "<br>";
}

$stmt->close();
$conn->close();
?>
