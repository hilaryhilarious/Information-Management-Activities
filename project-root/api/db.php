<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'eventrix_user');
define('DB_PASS', 'eventrix_pass');
define('DB_NAME', 'eventrix');

// Create connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die(json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]));
    }
    
    $conn->set_charset('utf8mb4');
    return $conn;
}

// Helper function to log audit trail
function logAudit($conn, $user_id, $action, $table_name, $record_id = null, $old_value = null, $new_value = null) {
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $stmt = $conn->prepare("INSERT INTO AuditLogs (user_id, action, table_name, record_id, old_value, new_value, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('issssss', $user_id, $action, $table_name, $record_id, $old_value, $new_value, $ip_address);
    $stmt->execute();
    $stmt->close();
}
?>