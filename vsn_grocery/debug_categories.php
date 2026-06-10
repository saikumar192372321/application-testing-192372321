<?php
include 'db_config.php';
$result = $conn->query("SELECT details FROM products");
while($row = $result->fetch_assoc()) {
    echo $row['details'] . "\n";
}
?>
