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
    // Get all participants
    $query = "SELECT p.*, t.team_name FROM Participants p LEFT JOIN Teams t ON p.team_id = t.team_id ORDER BY p.created_at DESC";
    $result = $conn->query($query);
    
    $participants = [];
    while ($row = $result->fetch_assoc()) {
        $participants[] = $row;
    }
    
    echo json_encode(['success' => true, 'participants' => $participants]);
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $_GET['action'] ?? 'create';
    
    if ($action === 'create') {
        $first_name = $data['first_name'] ?? '';
        $last_name = $data['last_name'] ?? '';
        $team_id = $data['team_id'] ?? null;
        $participant_number = $data['participant_number'] ?? null;
        $email = $data['email'] ?? null;
        $phone = $data['phone'] ?? null;
        
        $stmt = $conn->prepare("INSERT INTO Participants (first_name, last_name, team_id, participant_number, email, phone) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssisss', $first_name, $last_name, $team_id, $participant_number, $email, $phone);
        
        if ($stmt->execute()) {
            $participant_id = $stmt->insert_id;
            logAudit($conn, $_SESSION['user_id'], 'create_participant', 'Participants', $participant_id, null, json_encode($data));
            echo json_encode(['success' => true, 'participant_id' => $participant_id]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to create participant']);
        }
    }
}

$conn->close();
?>