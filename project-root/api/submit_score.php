<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$conn = getDBConnection();
$data = json_decode(file_get_contents('php://input'), true);

$event_id = $data['event_id'] ?? null;
$participant_id = $data['participant_id'] ?? null;
$team_id = $data['team_id'] ?? null;
$criteria_id = $data['criteria_id'] ?? null;
$score_value = $data['score_value'] ?? null;
$remarks = $data['remarks'] ?? '';

if (!$event_id || !$criteria_id || $score_value === null) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

if (!$participant_id && !$team_id) {
    echo json_encode(['success' => false, 'error' => 'Either participant_id or team_id is required']);
    exit;
}

// Get judge_id from user_id
$stmt = $conn->prepare("SELECT judge_id FROM Judges WHERE user_id = ?");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'User is not a judge']);
    exit;
}

$judge = $result->fetch_assoc();
$judge_id = $judge['judge_id'];

// Check if judge is assigned to this event
$stmt = $conn->prepare("SELECT event_judge_id FROM EventJudges WHERE event_id = ? AND judge_id = ?");
$stmt->bind_param('ii', $event_id, $judge_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Judge is not assigned to this event']);
    exit;
}

// Insert or update score
$stmt = $conn->prepare("INSERT INTO Scores (event_id, participant_id, team_id, judge_id, criteria_id, score_value, remarks) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE score_value = ?, remarks = ?");
$stmt->bind_param('iiiidsdsi', $event_id, $participant_id, $team_id, $judge_id, $criteria_id, $score_value, $remarks, $score_value, $remarks);

if ($stmt->execute()) {
    $score_id = $stmt->insert_id ?: $conn->insert_id;
    
    // Update ScoreTotals
    updateScoreTotals($conn, $event_id, $participant_id, $team_id);
    
    logAudit($conn, $_SESSION['user_id'], 'submit_score', 'Scores', $score_id, null, json_encode($data));
    
    echo json_encode(['success' => true, 'message' => 'Score submitted successfully']);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to submit score']);
}

// Function to update score totals
function updateScoreTotals($conn, $event_id, $participant_id, $team_id) {
    $target_id = $participant_id ?: $team_id;
    $target_field = $participant_id ? 'participant_id' : 'team_id';
    
    // Calculate weighted total score
    $query = "SELECT SUM(s.score_value * c.weight) as total_score FROM Scores s JOIN Criteria c ON s.criteria_id = c.criteria_id WHERE s.event_id = ? AND s.$target_field = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $event_id, $target_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $total_score = $row['total_score'] ?? 0;
    
    // Insert or update ScoreTotals
    if ($participant_id) {
        $stmt = $conn->prepare("INSERT INTO ScoreTotals (event_id, participant_id, total_score) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE total_score = ?");
        $stmt->bind_param('iidd', $event_id, $participant_id, $total_score, $total_score);
    } else {
        $stmt = $conn->prepare("INSERT INTO ScoreTotals (event_id, team_id, total_score) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE total_score = ?");
        $stmt->bind_param('iidd', $event_id, $team_id, $total_score, $total_score);
    }
    
    $stmt->execute();
    
    // Update rankings
    updateRankings($conn, $event_id);
}

// Function to update rankings
function updateRankings($conn, $event_id) {
    $query = "SELECT score_total_id, total_score FROM ScoreTotals WHERE event_id = ? ORDER BY total_score DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $rank = 1;
    while ($row = $result->fetch_assoc()) {
        $update_stmt = $conn->prepare("UPDATE ScoreTotals SET rank_position = ? WHERE score_total_id = ?");
        $update_stmt->bind_param('ii', $rank, $row['score_total_id']);
        $update_stmt->execute();
        $rank++;
    }
}

$conn->close();
?>