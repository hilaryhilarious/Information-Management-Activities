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

// Get event participants (both individuals and teams)
$query = "SELECT ep.*, p.first_name, p.last_name, p.participant_number, t.team_name, t.team_code FROM EventParticipants ep LEFT JOIN Participants p ON ep.participant_id = p.participant_id LEFT JOIN Teams t ON ep.team_id = t.team_id WHERE ep.event_id = ? ORDER BY ep.registration_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $event_id);
$stmt->execute();
$result = $stmt->get_result();

$participants = [];
while ($row = $result->fetch_assoc()) {
    $participants[] = $row;
}

echo json_encode(['success' => true, 'participants' => $participants]);

$conn->close();
?>