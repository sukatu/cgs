<?php
// API endpoint to fetch events for frontend
require_once 'config.php';
header('Content-Type: application/json');

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
$events = [];

while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

echo json_encode([
    'success' => true,
    'count' => count($events),
    'events' => $events
]);

$stmt->close();
$conn->close();
?>
