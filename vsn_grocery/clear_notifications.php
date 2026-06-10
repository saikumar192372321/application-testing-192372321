<?php
// clear_notifications.php
// Deletes all notifications for a specific user.
// Expected JSON body: { "userEmail": "user@example.com" }

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include 'db_config.php';

$input = json_decode(file_get_contents("php://input"), true);
$userEmail = isset($input['userEmail']) ? trim($input['userEmail']) : "";

if (empty($userEmail)) {
    echo json_encode(["status" => "error", "message" => "User Email is required."]);
    $conn->close();
    exit;
}

$stmt = $conn->prepare("DELETE FROM notifications WHERE userEmail = ? OR userEmail = 'all'");
$stmt->bind_param("s", $userEmail);

if ($stmt->execute()) {
    sendResponse("success", "All notifications cleared.");
} else {
    sendResponse("error", "Clear failed: " . $conn->error);
}

$stmt->close();
$conn->close();
?>
