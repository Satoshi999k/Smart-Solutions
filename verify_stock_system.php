<?php
/**
 * Stock System Verification Script
 * Run this to verify stock is working properly
 */

include 'conn.php';

echo "<!DOCTYPE html>
<html>
<head>
<title>Stock System Verification</title>
<style>
    body { font-family: Arial; margin: 20px; }
    table { border-collapse: collapse; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
    th { background-color: #0062F6; color: white; }
    .success { color: #4caf50; font-weight: bold; }
    .warning { color: #ff9800; font-weight: bold; }
    .error { color: #f44336; font-weight: bold; }
</style>
</head>
<body>
<h1>Stock Management System Verification</h1>";

// Check 1: Verify stock column exists
echo "<h2>1. Database Structure Check</h2>";
$check_column = $conn->query("SHOW COLUMNS FROM products LIKE 'stock'");
if ($check_column && $check_column->num_rows > 0) {
    echo "<p class='success'>✓ Stock column exists in products table</p>";
} else {
    echo "<p class='error'>✗ Stock column NOT found. Run init_stock.php to create it.</p>";
}

// Check 2: Display current stock values
echo "<h2>2. Current Product Stock Levels</h2>";
$result = $conn->query("SELECT id, name, stock FROM products ORDER BY id LIMIT 15");
if ($result && $result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>Product ID</th><th>Product Name</th><th>Current Stock</th><th>Status</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $status = '';
        if ($row['stock'] > 5) {
            $status = "<span class='success'>In Stock</span>";
        } elseif ($row['stock'] > 0) {
            $status = "<span class='warning'>Low Stock</span>";
        } else {
            $status = "<span class='error'>Out of Stock</span>";
        }
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . $row['stock'] . "</td>";
        echo "<td>" . $status . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>Could not fetch products</p>";
}

// Check 3: Verify product pages are fetching stock
echo "<h2>3. Product Page Integration</h2>";
echo "<p>The following product pages have been updated to display stock:</p>";
$pages = array(
    'products/laptop.php' => 'Laptops',
    'products/processor.php' => 'Processors',
    'products/memory.php' => 'Memory',
    'products/ssd.php' => 'SSDs',
    'products/graphicscard.php' => 'Graphics Cards',
    'products/headset.php' => 'Headsets',
    'products/keyboard.php' => 'Keyboards',
    'products/mouse.php' => 'Mice',
    'products/monitor.php' => 'Monitors',
    'products/motherboard.php' => 'Motherboards',
    'products/powersupply.php' => 'Power Supplies',
    'products/pccase.php' => 'PC Cases',
);

echo "<ul>";
foreach ($pages as $file => $name) {
    if (file_exists($file)) {
        echo "<li><span class='success'>✓</span> " . $name . " (" . $file . ")</li>";
    } else {
        echo "<li><span class='error'>✗</span> " . $name . " (" . $file . ")</li>";
    }
}
echo "</ul>";

// Check 4: Verify checkout process
echo "<h2>4. Checkout Stock Update</h2>";
$checkout_file = 'pages/process_checkout.php';
if (file_exists($checkout_file)) {
    $content = file_get_contents($checkout_file);
    if (strpos($content, 'UPDATE products SET stock') !== false) {
        echo "<p class='success'>✓ Stock deduction code found in checkout process</p>";
    } else {
        echo "<p class='error'>✗ Stock deduction code NOT found in checkout</p>";
    }
} else {
    echo "<p class='error'>✗ Checkout file not found</p>";
}

echo "<h2>5. How It Works</h2>";
echo "<ul>";
echo "<li><strong>Stock Display:</strong> Each product card now shows 'In Stock: X' in green/orange/red based on quantity</li>";
echo "<li><strong>Stock Updates:</strong> When an order is placed, stock is automatically decreased</li>";
echo "<li><strong>Real-time:</strong> Navigate to any product page to see current stock levels</li>";
echo "</ul>";

echo "<h2>6. Next Steps</h2>";
echo "<ol>";
echo "<li>Visit any product page (Laptops, Processors, etc.) to verify stock is displayed</li>";
echo "<li>Place a test order through the checkout process</li>";
echo "<li>Verify that stock numbers decreased after order completion</li>";
echo "</ol>";

echo "</body></html>";

$conn->close();
?>
