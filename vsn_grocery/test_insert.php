<?php
require_once 'db_config.php';
$id = 'TEST-' . time();
$name = 'Test Product ' . time();
$stmt = $pdo->prepare("INSERT INTO products (id, name, retail_price, wholesale_price) VALUES (?, ?, 100, 80)");
$res = $stmt->execute([$id, $name]);
if ($res) {
    echo "SUCCESS: Saved $name with ID $id\n";
} else {
    echo "ERROR: Failed to save\n";
}
?>
