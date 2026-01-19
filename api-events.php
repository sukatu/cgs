<?php
// API endpoint to fetch events for frontend
require_once 'config.php';
header('Content-Type: application/json');

// CGS II Event - Define early so it can be used as fallback
$cgsIIEvent = [
    'id' => 999,
    'title' => 'CGS II Bank Corporate Governance and Financial Stability: The Role of Bank Boards',
    'description' => 'Join us for CGS II, a critical discussion on bank corporate governance and financial stability, focusing on the essential role of bank boards in ensuring robust governance frameworks and financial stability in the banking sector.' . "\n\n" . 'Zoom Meeting Details:' . "\n" . 'Meeting ID: 885 0243 0789' . "\n" . 'Passcode: 822412' . "\n\n" . 'Join instructions: https://us06web.zoom.us/meetings/88502430789/invitations?signature=jv3kLZCqPxnGY0kOXjKJ-j_yX8d2Rbww5hhLcVJeOWA',
    'event_type' => 'series',
    'event_date' => '2026-02-12 17:00:00',
    'location' => 'Dr. Daniel McKorley Moot Court Room, GIMPA Law School',
    'format' => 'hybrid',
    'registration_link' => 'https://us06web.zoom.us/j/88502430789?pwd=e3a79VijbjKZTolGnhZDoaN4s7OIug.1',
    'youtube_url' => null,
    'agenda' => null,
    'speakers' => null,
    'moderator' => null,
    'summary' => null,
    'tags' => null,
    'countries' => null,
    'status' => 'upcoming',
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s')
];

$events = [];
$dbError = false;

try {
    $conn = getDBConnection();

    $type = $_GET['type'] ?? null;
    $status = $_GET['status'] ?? null;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : null;

    $sql = "SELECT * FROM events WHERE 1=1";
    $params = [];
    $types = [];

    if ($type) {
        $sql .= " AND event_type = ?";
        $params[] = $type;
        $types[] = "s";
    }

    if ($status) {
        $sql .= " AND status = ?";
        $params[] = $status;
        $types[] = "s";
    }

    $sql .= " ORDER BY event_date DESC, created_at DESC";

    if ($limit) {
        $sql .= " LIMIT ?";
        $params[] = $limit;
        $types[] = "i";
    }

    $stmt = $conn->prepare($sql);

    if (!empty($params)) {
        $stmt->bind_param(implode('', $types), ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    // Database connection failed - use fallback for local testing
    $dbError = true;
    error_log("API Events Error: " . $e->getMessage());
}

// If database error, return only CGS II event for local testing
if ($dbError) {
    $type = $_GET['type'] ?? null;
    $status = $_GET['status'] ?? null;
    
    $shouldAdd = true;
    
    // Check type filter
    if ($type && $cgsIIEvent['event_type'] !== $type) {
        $shouldAdd = false;
    }
    
    // Check status filter
    if ($status && $cgsIIEvent['status'] !== $status) {
        $shouldAdd = false;
    }
    
    if ($shouldAdd) {
        $events = [$cgsIIEvent];
    }
} else {
    // Check if CGS II event already exists in results
    $cgsIIExists = false;
    foreach ($events as $event) {
        if (stripos($event['title'], 'CGS II') !== false && 
            isset($event['event_date']) && 
            $event['event_date'] === '2026-02-12 17:00:00') {
            $cgsIIExists = true;
            break;
        }
    }

    // Add CGS II event if it doesn't exist and matches the filter criteria
    if (!$cgsIIExists) {
        $type = $_GET['type'] ?? null;
        $status = $_GET['status'] ?? null;
        
        $shouldAdd = true;
        
        // Check type filter
        if ($type && $cgsIIEvent['event_type'] !== $type) {
            $shouldAdd = false;
        }
        
        // Check status filter
        if ($status && $cgsIIEvent['status'] !== $status) {
            $shouldAdd = false;
        }
        
        if ($shouldAdd) {
            // Insert at the beginning for upcoming events (most recent first)
            array_unshift($events, $cgsIIEvent);
        }
    }
}

echo json_encode([
    'success' => true,
    'count' => count($events),
    'events' => $events
]);
?>
