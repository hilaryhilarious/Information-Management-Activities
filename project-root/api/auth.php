<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

$conn = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($action === 'login') {
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'error' => 'Email and password are required']);
            exit;
        }
        
        $stmt = $conn->prepare("SELECT u.user_id, u.email, u.password, u.first_name, u.last_name, u.status, r.role_name FROM Users u JOIN Roles r ON u.role_id = r.role_id WHERE u.email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid credentials']);
            exit;
        }
        
        $user = $result->fetch_assoc();
        
        if ($user['status'] !== 'active') {
            echo json_encode(['success' => false, 'error' => 'Account is inactive']);
            exit;
        }
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role_name'];
            $_SESSION['name'] = $user['first_name'] . ' ' . $user['last_name'];
            
            logAudit($conn, $user['user_id'], 'login', 'Users', $user['user_id'], null, null);
            
            echo json_encode([
                'success' => true,
                'user' => [
                    'user_id' => $user['user_id'],
                    'email' => $user['email'],
                    'name' => $_SESSION['name'],
                    'role' => $user['role_name']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid credentials']);
        }
        
        $stmt->close();
    } elseif ($action === 'register') {
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $first_name = $data['first_name'] ?? '';
        $last_name = $data['last_name'] ?? '';
        $role_id = 5; // Default to General User
        
        if (empty($email) || empty($password) || empty($first_name) || empty($last_name)) {
            echo json_encode(['success' => false, 'error' => 'All fields are required']);
            exit;
        }
        
        // Check if email already exists
        $stmt = $conn->prepare("SELECT user_id FROM Users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'error' => 'Email already exists']);
            exit;
        }
        
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt = $conn->prepare("INSERT INTO Users (email, password, first_name, last_name, role_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssi', $email, $hashed_password, $first_name, $last_name, $role_id);
        
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            logAudit($conn, $user_id, 'register', 'Users', $user_id, null, json_encode(['email' => $email]));
            
            echo json_encode(['success' => true, 'message' => 'Registration successful']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Registration failed']);
        }
        
        $stmt->close();
    } elseif ($action === 'logout') {
        if (isset($_SESSION['user_id'])) {
            logAudit($conn, $_SESSION['user_id'], 'logout', 'Users', $_SESSION['user_id'], null, null);
        }
        session_destroy();
        echo json_encode(['success' => true]);
    }
} elseif ($method === 'GET' && $action === 'check') {
    if (isset($_SESSION['user_id'])) {
        echo json_encode([
            'success' => true,
            'authenticated' => true,
            'user' => [
                'user_id' => $_SESSION['user_id'],
                'email' => $_SESSION['email'],
                'name' => $_SESSION['name'],
                'role' => $_SESSION['role']
            ]
        ]);
    } else {
        echo json_encode(['success' => true, 'authenticated' => false]);
    }
}

$conn->close();
?>