<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

$conn = getDBConnection();
$event_id = $_GET['event_id'] ?? null;

if (!$event_id) {
    echo json_encode(['success' => false, 'error' => 'Event ID is required']);
    exit;
}

// Get all scores for an event with participant/team info
$query = "SELECT s.*, c.criteria_name, c.weight, p.first_name as p_first_name, p.last_name as p_last_name, t.team_name, u.first_name as j_first_name, u.last_name as j_last_name FROM Scores s LEFT JOIN Criteria c ON s.criteria_id = c.criteria_id LEFT JOIN Participants p ON s.participant_id = p.participant_id LEFT JOIN Teams t ON s.team_id = t.team_id LEFT JOIN Judges j ON s.judge_id = j.judge_id LEFT JOIN Users u ON j.user_id = u.user_id WHERE s.event_id = ? ORDER BY s.submitted_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $event_id);
$stmt->execute();
$result = $stmt->get_result();

$scores = [];
while ($row = $result->fetch_assoc()) {
    $scores[] = $row;
}

echo json_encode(['success' => true, 'scores' => $scores]);

$conn->close();
?>