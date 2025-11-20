<?php
// Check and initialize stock data for all products
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Database Stock Check</h2>";

// Check products table
$check = $conn->query("SELECT COUNT(*) as count FROM products");
$row = $check->fetch_assoc();
$total = $row['count'];

echo "<p>Total products in database: <strong>$total</strong></p>";

if ($total > 0) {
    // Check if stock column exists
    $check_column = $conn->query("SHOW COLUMNS FROM products LIKE 'stock'");
    
    if ($check_column->num_rows == 0) {
        echo "<p style='color: red;'>⚠️ Stock column does NOT exist. Creating it...</p>";
        if ($conn->query("ALTER TABLE products ADD COLUMN stock INT DEFAULT 10")) {
            echo "<p style='color: green;'>✓ Stock column created successfully!</p>";
        }
    } else {
        echo "<p style='color: green;'>✓ Stock column exists</p>";
    }
    
    // Check how many products have 0 or NULL stock
    $check_zero = $conn->query("SELECT COUNT(*) as count FROM products WHERE stock IS NULL OR stock = 0");
    $row = $check_zero->fetch_assoc();
    $zero_count = $row['count'];
    
    if ($zero_count > 0) {
        echo "<p style='color: orange;'>⚠️ Found $zero_count products with 0 or NULL stock. Initializing to 15...</p>";
        if ($conn->query("UPDATE products SET stock = 15 WHERE stock IS NULL OR stock = 0")) {
            echo "<p style='color: green;'>✓ Stock initialized for all products!</p>";
        }
    }
    
    // Show sample stock data
    echo "<h3>Sample Stock Data (First 10 Products):</h3>";
    $result = $conn->query("SELECT id, name, stock FROM products LIMIT 10");
    
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr style='background: #f0f0f0;'><th>Product ID</th><th>Product Name</th><th>Stock</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        $name = strlen($row['name']) > 40 ? substr($row['name'], 0, 40) . '...' : $row['name'];
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $name . "</td>";
        echo "<td><strong style='color: #0062F6; font-size: 16px;'>" . $row['stock'] . "</strong></td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<hr>";
    echo "<p><strong>✓ Stock system is ready!</strong></p>";
    echo "<p>Now when you click 'Add to Cart' on any product, you'll see the stock information displayed in the modal.</p>";
    
} else {
    echo "<p style='color: red;'>❌ No products found in database. Please add products first.</p>";
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Stock Database Check</title>
    <style>
        body { font-family: Arial; padding: 20px; max-width: 900px; margin: 0 auto; }
        table { margin: 20px 0; }
        td { padding: 12px; border: 1px solid #ddd; }
        th { background: #f0f0f0; }
        p { line-height: 1.6; }
        a { color: #0062F6; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>Smart Solutions - Stock Database Check</h1>
    <hr>
</body>
</html>
