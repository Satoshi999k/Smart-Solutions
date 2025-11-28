<?php

$conn = mysqli_connect('localhost','root','','smartsolutions') or die('connection failed');

// Auto-initialize stock column if missing (runs once per connection)
$check_stock = $conn->query("SHOW COLUMNS FROM products LIKE 'stock'");
if (!$check_stock || $check_stock->num_rows == 0) {
    // Add stock column with default value 10
    $conn->query("ALTER TABLE products ADD COLUMN stock INT DEFAULT 10 NOT NULL");
    // Initialize all existing products with stock = 10
    $conn->query("UPDATE products SET stock = 10 WHERE stock IS NULL OR stock = 0");
}

?>