<?php
// hash_admin_passwords.php
require_once 'db_config.php';

echo "Hashing Admin Passwords...\n";

$result = $conn->query("SELECT email, password FROM admins");

while ($row = $result->fetch_assoc()) {
    $email = $row['email'];
    $pass  = $row['password'];
    
    // Check if already hashed (bcrypt hashes start with $2y$)
    if (strpos($pass, '$2y$') !== 0) {
        $hashed = password_hash($pass, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE admins SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed, $email);
        $stmt->execute();
        echo "✓ Hashed password for $email\n";
        $stmt->close();
    } else {
        echo "– Password for $email is already hashed.\n";
    }
}

echo "Done.\n";
$conn->close();
?>
