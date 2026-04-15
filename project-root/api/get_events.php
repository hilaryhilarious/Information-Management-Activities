<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

$conn = getDBConnection();
$event_id = $_GET['event_id'] ?? null;
$status = $_GET['status'] ?? null;

if ($event_id) {
    // Get single event with details
    $stmt = $conn->prepare("SELECT e.*, u.first_name, u.last_name, COUNT(DISTINCT ep.event_participant_id) as participant_count FROM Events e LEFT JOIN Users u ON e.created_by = u.user_id LEFT JOIN EventParticipants ep ON e.event_id = ep.event_id WHERE e.event_id = ? GROUP BY e.event_id");
    $stmt->bind_param('i', $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $event = $result->fetch_assoc();
        echo json_encode(['success' => true, 'event' => $event]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Event not found']);
    }
} else {
    // Get all events
    $query = "SELECT e.*, u.first_name, u.last_name, COUNT(DISTINCT ep.event_participant_id) as participant_count FROM Events e LEFT JOIN Users u ON e.created_by = u.user_id LEFT JOIN EventParticipants ep ON e.event_id = ep.event_id";
    
    if ($status) {
        $query .= " WHERE e.status = ?";
    }
    
    $query .= " GROUP BY e.event_id ORDER BY e.created_at DESC";
    
    if ($status) {
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $status);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($query);
    }
    
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
    
    echo json_encode(['success' => true, 'events' => $events]);
}

$conn->close();
?>