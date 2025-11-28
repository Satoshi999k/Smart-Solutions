<?php
$conn = new mysqli("localhost", "root", "", "smartsolutions");

// Check processor (ID 22)
$result = $conn->query("SELECT id, name, stock FROM products WHERE id = 22");
if ($result && $row = $result->fetch_assoc()) {
    echo "Product 22: " . $row['name'] . " | Stock: " . $row['stock'] . "<br>";
}

// Check first 5 products
echo "<hr>";
$result = $conn->query("SELECT id, name, stock FROM products LIMIT 5");
while ($row = $result->fetch_assoc()) {
    echo "ID " . $row['id'] . ": " . $row['name'] . " | Stock: " . $row['stock'] . "<br>";
}
?>
