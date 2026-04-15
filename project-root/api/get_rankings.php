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

// Get rankings for an event
$query = "SELECT st.*, p.first_name as p_first_name, p.last_name as p_last_name, p.participant_number, t.team_name, t.team_code FROM ScoreTotals st LEFT JOIN Participants p ON st.participant_id = p.participant_id LEFT JOIN Teams t ON st.team_id = t.team_id WHERE st.event_id = ? ORDER BY st.rank_position ASC, st.total_score DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $event_id);
$stmt->execute();
$result = $stmt->get_result();

$rankings = [];
while ($row = $result->fetch_assoc()) {
    $rankings[] = $row;
}

echo json_encode(['success' => true, 'rankings' => $rankings]);

$conn->close();
?>