<?php
// forgot_password.php - Generate and send OTP
// NOTE: db_config.php already sets Content-Type: application/json
require_once "db_config.php";
require_once "vsn_mailer.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse("error", "Method not allowed. Use POST.", null, 405);
}

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['email'])) {
    sendResponse("error", "Email is required.", null, 400);
}

$email = strtolower(trim($data['email']));

try {
    // 1. Check if user exists
    $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        sendResponse("error", "Email not registered");
    }

    // 2. Generate OTP
    $otp = generateOTP();
    $expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));

    // 3. Save to DB
    $stmt = $pdo->prepare("UPDATE users SET otp = :otp, otp_expiry = :expiry WHERE email = :email");
    $stmt->execute([
        ':otp' => $otp,
        ':expiry' => $expiry,
        ':email' => $email
    ]);

    // 4. Send Email
    $subject = "Your VSN Home Reset Code: $otp";
    $message = "
        <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee;'>
            <h2 style='color: #2ecc71;'>VSN Home</h2>
            <p>Hello <b>" . htmlspecialchars($user['name']) . "</b>,</p>
            <p>You requested a password reset. Use the code below to proceed:</p>
            <div style='background: #f4f4f4; padding: 15px; font-size: 24px; font-weight: bold; text-align: center; letter-spacing: 5px;'>
                $otp
            </div>
            <p>This code will expire in 15 minutes.</p>
            <p>If you didn't request this, please ignore this email.</p>
        </div>";

    if (vsn_send_email($email, $subject, $message)) {
        sendResponse("success", "OTP sent to your email");
    } else {
        // Fallback: If mail fails, for testing we might return it in response (NOT SECURE FOR PRODUCTION)
        sendResponse("success", "OTP generated (Simulation)"); // Use real SMTP info in vsn_mailer.php
    }

} catch (PDOException $e) {
    sendResponse("error", $e->getMessage());
}
?>
