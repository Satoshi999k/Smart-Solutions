<?php
/**
 * Auto-initialize Stock Column if Missing
 * This runs at the beginning and fixes any missing stock columns
 */

$conn = new mysqli("localhost", "root", "", "smartsolutions");

if (!$conn->connect_error) {
    // Check if stock column exists
    $check = $conn->query("SHOW COLUMNS FROM products LIKE 'stock'");
    
    if (!$check || $check->num_rows == 0) {
        // Add stock column
        $conn->query("ALTER TABLE products ADD COLUMN stock INT DEFAULT 10 NOT NULL");
        
        // Initialize all products with stock = 10
        $conn->query("UPDATE products SET stock = 10 WHERE stock IS NULL OR stock = 0");
    }
    
    $conn->close();
}
?>
