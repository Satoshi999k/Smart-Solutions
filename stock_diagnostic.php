<?php
/**
 * COMPLETE STOCK SYSTEM DIAGNOSTIC
 * Run this page to see the complete status and troubleshoot any issues
 */

session_start();
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Stock System Diagnostic</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #0062F6; border-bottom: 3px solid #0062F6; padding-bottom: 10px; }
        h2 { color: #333; margin-top: 30px; }
        .section { margin: 20px 0; padding: 15px; background: #f9f9f9; border-left: 4px solid #0062F6; }
        .success { color: #4caf50; font-weight: bold; }
        .error { color: #f44336; font-weight: bold; }
        .warning { color: #ff9800; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #0062F6; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .code { background: #f0f0f0; padding: 10px; border-radius: 4px; font-family: monospace; margin: 10px 0; overflow-x: auto; }
        ul { line-height: 1.8; }
    </style>
</head>
<body>
<div class="container">
<h1>Stock Management System - Complete Diagnostic</h1>

<?php

// TEST 1: Stock Column Existence
echo "<h2>TEST 1: Database Structure</h2>";
$check_column = $conn->query("SHOW COLUMNS FROM products LIKE 'stock'");

if ($check_column && $check_column->num_rows > 0) {
    $col = $check_column->fetch_assoc();
    echo "<div class='section'>";
    echo "<p class='success'>✓ Stock column EXISTS in products table</p>";
    echo "<p><strong>Column Details:</strong></p>";
    echo "<ul>";
    echo "<li>Type: " . $col['Type'] . "</li>";
    echo "<li>Null: " . $col['Null'] . "</li>";
    echo "<li>Default: " . $col['Default'] . "</li>";
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div class='section'>";
    echo "<p class='error'>✗ Stock column DOES NOT EXIST</p>";
    echo "<p>Creating it now...</p>";
    if ($conn->query("ALTER TABLE products ADD COLUMN stock INT DEFAULT 10 NOT NULL")) {
        echo "<p class='success'>✓ Stock column created</p>";
    } else {
        echo "<p class='error'>✗ Failed to create: " . $conn->error . "</p>";
    }
    echo "</div>";
}

// TEST 2: Stock Values
echo "<h2>TEST 2: Stock Data Status</h2>";
$stats = $conn->query("SELECT COUNT(*) as total, COUNT(stock) as with_stock, SUM(stock) as total_stock FROM products");
$stat = $stats->fetch_assoc();

echo "<div class='section'>";
echo "<p><strong>Products in Database:</strong> " . $stat['total'] . "</p>";
echo "<p><strong>Products with Stock Data:</strong> " . $stat['with_stock'] . "</p>";
echo "<p><strong>Total Stock Available:</strong> " . ($stat['total_stock'] ?? 0) . " units</p>";

if ($stat['with_stock'] < $stat['total']) {
    echo "<p class='warning'>⚠ Some products missing stock data</p>";
} else {
    echo "<p class='success'>✓ All products have stock data</p>";
}
echo "</div>";

// TEST 3: Sample Products with Stock
echo "<h2>TEST 3: Sample Products (First 10)</h2>";
$sample = $conn->query("SELECT id, name, price, stock, category FROM products ORDER BY id LIMIT 10");

echo "<table>";
echo "<tr>";
echo "<th>ID</th>";
echo "<th>Product Name</th>";
echo "<th>Category</th>";
echo "<th>Price (P)</th>";
echo "<th>Stock</th>";
echo "<th>Status</th>";
echo "</tr>";

while ($row = $sample->fetch_assoc()) {
    $status = ($row['stock'] > 5) ? '<span class="success">In Stock</span>' : 
              (($row['stock'] > 0) ? '<span class="warning">Low Stock</span>' : '<span class="error">Out</span>');
    
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . htmlspecialchars(substr($row['name'], 0, 50)) . "</td>";
    echo "<td>" . htmlspecialchars($row['category']) . "</td>";
    echo "<td>" . number_format($row['price'], 2) . "</td>";
    echo "<td><strong>" . $row['stock'] . "</strong></td>";
    echo "<td>" . $status . "</td>";
    echo "</tr>";
}
echo "</table>";

// TEST 4: Current Session Cart
echo "<h2>TEST 4: Current Shopping Cart (Session)</h2>";
$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo "<div class='section'>";
    echo "<p class='warning'>Shopping cart is empty</p>";
    echo "</div>";
} else {
    echo "<table>";
    echo "<tr>";
    echo "<th>Product ID</th>";
    echo "<th>Name</th>";
    echo "<th>Quantity</th>";
    echo "<th>Unit Price</th>";
    echo "<th>Subtotal</th>";
    echo "<th>Stock Impact</th>";
    echo "</tr>";
    
    $total = 0;
    foreach ($cart as $item) {
        $qty = $item['quantity'] ?? 1;
        $price = $item['price'] ?? 0;
        $subtotal = $qty * $price;
        $total += $subtotal;
        
        // Get current stock
        $current_stock = 0;
        $stock_query = $conn->query("SELECT stock FROM products WHERE id = " . intval($item['id']));
        if ($stock_query && $stock_query->num_rows > 0) {
            $s = $stock_query->fetch_assoc();
            $current_stock = $s['stock'];
        }
        
        echo "<tr>";
        echo "<td>" . $item['id'] . "</td>";
        echo "<td>" . htmlspecialchars($item['name'] ?? 'N/A') . "</td>";
        echo "<td>" . $qty . "</td>";
        echo "<td>₱" . number_format($price, 2) . "</td>";
        echo "<td>₱" . number_format($subtotal, 2) . "</td>";
        echo "<td>" . $current_stock . " - " . ($current_stock - $qty) . "</td>";
        echo "</tr>";
    }
    echo "<tr style='font-weight: bold; background: #e3f2fd;'>";
    echo "<td colspan='4'>TOTAL</td>";
    echo "<td>₱" . number_format($total, 2) . "</td>";
    echo "<td></td>";
    echo "</tr>";
    echo "</table>";
}

// TEST 5: Prepared Statement Test
echo "<h2>TEST 5: Stock Update Test (Prepared Statement)</h2>";
echo "<div class='section'>";

$test_prod = $conn->query("SELECT id, stock FROM products WHERE stock > 0 LIMIT 1");
if ($test_prod && $test_prod->num_rows > 0) {
    $prod = $test_prod->fetch_assoc();
    $test_id = $prod['id'];
    $original = $prod['stock'];
    
    echo "<p>Testing with Product ID: " . $test_id . " (Current Stock: " . $original . ")</p>";
    
    // Prepare test statement
    $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
    if ($stmt) {
        $test_qty = 1;
        $stmt->bind_param("ii", $test_qty, $test_id);
        
        if ($stmt->execute()) {
            echo "<p class='success'>✓ Prepared statement executed</p>";
            
            // Check result
            $verify = $conn->query("SELECT stock FROM products WHERE id = $test_id");
            $v = $verify->fetch_assoc();
            
            echo "<p>Stock after test: " . $v['stock'] . "</p>";
            
            if ($v['stock'] == $original - 1) {
                echo "<p class='success'>✓ Stock decreased correctly!</p>";
            }
            
            // Restore
            $restore = $conn->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
            $restore->bind_param("ii", $test_qty, $test_id);
            $restore->execute();
            $restore->close();
            
        } else {
            echo "<p class='error'>✗ Execute failed: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p class='error'>✗ Prepared statement failed: " . $conn->error . "</p>";
    }
} else {
    echo "<p class='warning'>No products with stock > 0 found</p>";
}
echo "</div>";

// TEST 6: Product Pages Status
echo "<h2>TEST 6: Product Pages Integration</h2>";
echo "<div class='section'>";

$pages = [
    'products/laptop.php', 'products/processor.php', 'products/memory.php',
    'products/ssd.php', 'products/graphicscard.php', 'products/headset.php',
    'products/keyboard.php', 'products/mouse.php', 'products/monitor.php',
    'products/motherboard.php', 'products/powersupply.php', 'products/pccase.php'
];

$updated_count = 0;
foreach ($pages as $page) {
    if (file_exists($page)) {
        $content = file_get_contents($page);
        if (strpos($content, 'stock') !== false && strpos($content, 'In Stock') !== false) {
            $updated_count++;
        }
    }
}

echo "<p><strong>Product Pages Updated:</strong> " . $updated_count . " / " . count($pages) . "</p>";
if ($updated_count == count($pages)) {
    echo "<p class='success'>✓ All product pages have stock integration</p>";
} else {
    echo "<p class='warning'>⚠ Some pages may need updating</p>";
}
echo "</div>";

// FINAL STATUS
echo "<h2>Final Status</h2>";
echo "<div class='section'>";

$is_ready = ($check_column && $check_column->num_rows > 0) && 
            ($stat['with_stock'] == $stat['total']) && 
            ($updated_count == count($pages));

if ($is_ready) {
    echo "<h3 style='color: #4caf50;'>✓ STOCK SYSTEM IS READY!</h3>";
    echo "<p>The system is fully configured. Stock will be decremented when orders are placed.</p>";
} else {
    echo "<h3 style='color: #ff9800;'>⚠ SYSTEM NEEDS ATTENTION</h3>";
    echo "<p>Some components need attention. Review the tests above.</p>";
}

echo "</div>";

$conn->close();
?>

</div>
</body>
</html>
