<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$conn = getDBConnection();
$event_id = $_GET['event_id'] ?? null;

if (!$event_id) {
    echo json_encode(['success' => false, 'error' => 'Event ID is required']);
    exit;
}

// Get criteria for an event
$query = "SELECT * FROM Criteria WHERE event_id = ? ORDER BY criteria_id ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $event_id);
$stmt->execute();
$result = $stmt->get_result();

$criteria = [];
while ($row = $result->fetch_assoc()) {
    $criteria[] = $row;
}

echo json_encode(['success' => true, 'criteria' => $criteria]);

$conn->close();
?>