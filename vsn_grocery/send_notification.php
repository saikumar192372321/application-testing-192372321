<?php
// send_notification.php
// Receives a broadcast notification from Admin and stores it for all users.
// Expected JSON body: { "id": "uuid", "title": "...", "message": "...", "type": "general|offer|order", "userEmail": "all", "date": "2024-01-01 12:00:00", "isRead": false }

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ── DB Config ─────────────────────────────────────────────────────────────────
include 'db_config.php';

// ── Auto-create table if not exists ──────────────────────────────────────────
$conn->query("
    CREATE TABLE IF NOT EXISTS notifications (
        id VARCHAR(255) PRIMARY KEY,
        title VARCHAR(255),
        message TEXT,
        date DATETIME DEFAULT CURRENT_TIMESTAMP,
        isRead TINYINT(1) DEFAULT 0,
        type VARCHAR(50) DEFAULT 'General',
        userEmail VARCHAR(255) DEFAULT 'all'
    )
");

// ── Read Input ────────────────────────────────────────────────────────────────
$input = json_decode(file_get_contents("php://input"), true);

$id         = isset($input['id'])         ? trim($input['id'])         : bin2hex(random_bytes(16));
$title      = isset($input['title'])      ? trim($input['title'])      : "";
$message    = isset($input['message'])    ? trim($input['message'])    : "";
$type       = isset($input['type'])       ? trim($input['type'])       : "general";
$userEmail  = isset($input['userEmail'])  ? trim($input['userEmail'])  : "all";
$date       = isset($input['date'])       ? trim($input['date'])       : date("Y-m-d H:i:s");

if (empty($title) || empty($message)) {
    echo json_encode(["status" => "error", "message" => "Title and message are required."]);
    $conn->close();
    exit;
}

// ── Insert Notification ───────────────────────────────────────────────────────
$stmt = $conn->prepare("INSERT INTO notifications (id, title, message, type, userEmail, isRead, date) VALUES (?, ?, ?, ?, ?, 0, ?)");
$stmt->bind_param("ssssss", $id, $title, $message, $type, $userEmail, $date);

if ($stmt->execute()) {
    sendResponse("success", "Notification broadcast successful");
} else {
    sendResponse("error", "Insert failed: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>
