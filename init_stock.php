<?php
// Initialize stock for all products if not already set
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if products table exists
$check_table = $conn->query("SHOW TABLES LIKE 'products'");
if ($check_table->num_rows == 0) {
    echo "Products table does not exist.";
    $conn->close();
    exit;
}

// Check if stock column exists
$check_column = $conn->query("SHOW COLUMNS FROM products LIKE 'stock'");
if ($check_column->num_rows == 0) {
    echo "Adding stock column to products table...";
    $conn->query("ALTER TABLE products ADD COLUMN stock INT DEFAULT 10");
    echo " Done!<br>";
}

// Update products with NULL or 0 stock to have default stock value
$result = $conn->query("SELECT COUNT(*) as count FROM products WHERE stock IS NULL OR stock = 0");
$row = $result->fetch_assoc();
$count = $row['count'];

if ($count > 0) {
    echo "Initializing stock for $count products...<br>";
    
    // Set default stock of 15 for all products that don't have stock
    $conn->query("UPDATE products SET stock = 15 WHERE stock IS NULL OR stock = 0");
    
    echo "✓ Stock initialized!<br>";
} else {
    echo "✓ All products already have stock data.<br>";
}

// Show current stock data
echo "<hr>";
echo "<h3>Product Stock Summary:</h3>";
$result = $conn->query("SELECT id, name, stock FROM products ORDER BY id LIMIT 10");

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Name</th><th>Stock</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . substr($row['name'], 0, 50) . "...</td>";
    echo "<td><strong>" . $row['stock'] . "</strong></td>";
    echo "</tr>";
}
echo "</table>";

echo "<hr>";
echo "<p><a href='javascript:history.back()'>← Back to product page</a></p>";

$conn->close();
?>
