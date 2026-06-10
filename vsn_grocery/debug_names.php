<?php
include 'db_config.php';
$result = $conn->query("SELECT name, details FROM products");
while($row = $result->fetch_assoc()) {
    echo $row['name'] . " | " . $row['details'] . "\n";
}
?>
