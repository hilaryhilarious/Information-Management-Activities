<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$conn = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Get all teams
    $query = "SELECT t.*, COUNT(p.participant_id) as member_count FROM Teams t LEFT JOIN Participants p ON t.team_id = p.team_id GROUP BY t.team_id ORDER BY t.created_at DESC";
    $result = $conn->query($query);
    
    $teams = [];
    while ($row = $result->fetch_assoc()) {
        $teams[] = $row;
    }
    
    echo json_encode(['success' => true, 'teams' => $teams]);
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $_GET['action'] ?? 'create';
    
    if ($action === 'create') {
        $team_name = $data['team_name'] ?? '';
        $team_code = $data['team_code'] ?? '';
        $description = $data['description'] ?? '';
        
        $stmt = $conn->prepare("INSERT INTO Teams (team_name, team_code, description) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $team_name, $team_code, $description);
        
        if ($stmt->execute()) {
            $team_id = $stmt->insert_id;
            logAudit($conn, $_SESSION['user_id'], 'create_team', 'Teams', $team_id, null, json_encode($data));
            echo json_encode(['success' => true, 'team_id' => $team_id]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to create team']);
        }
    }
}

$conn->close();
?>