<?php
/**
 * Stock Quantity Verification
 * Check what's in the cart and what will be used for stock updates
 */
session_start();

$conn = new mysqli("localhost", "root", "", "smartsolutions");

echo "<!DOCTYPE html>
<html>
<head><title>Cart Stock Verification</title>
<style>
body { font-family: Arial; margin: 20px; }
table { border-collapse: collapse; margin: 10px 0; width: 100%; }
th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
th { background-color: #0062F6; color: white; }
.success { color: #4caf50; font-weight: bold; }
.error { color: #f44336; font-weight: bold; }
</style>
</head>
<body>
<h1>Cart & Stock Verification</h1>";

// Show session cart
echo "<h2>Session Cart Contents:</h2>";
$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo "<p class='error'>Cart is empty!</p>";
} else {
    echo "<table>";
    echo "<tr><th>Product ID</th><th>Product Name</th><th>Price</th><th>Quantity</th><th>Subtotal</th></tr>";
    
    foreach ($cart as $item) {
        $qty = $item['quantity'] ?? 1;
        $subtotal = ($item['price'] ?? 0) * $qty;
        
        echo "<tr>";
        echo "<td>" . $item['id'] . "</td>";
        echo "<td>" . htmlspecialchars($item['name'] ?? 'Unknown') . "</td>";
        echo "<td>₱" . number_format($item['price'] ?? 0, 2) . "</td>";
        echo "<td>" . $qty . "</td>";
        echo "<td>₱" . number_format($subtotal, 2) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Show what will happen during checkout
echo "<h2>What Will Happen During Checkout:</h2>";
echo "<table>";
echo "<tr><th>Product</th><th>Current Stock</th><th>Qty Buying</th><th>Stock After</th></tr>";

foreach ($cart as $item) {
    $product_id = $item['id'];
    $qty = $item['quantity'] ?? 1;
    
    $stock_query = "SELECT id, name, stock FROM products WHERE id = " . intval($product_id);
    $stock_result = $conn->query($stock_query);
    
    if ($stock_result && $stock_result->num_rows > 0) {
        $prod = $stock_result->fetch_assoc();
        $current_stock = $prod['stock'] ?? 0;
        $new_stock = $current_stock - $qty;
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($prod['name']) . "</td>";
        echo "<td>" . $current_stock . "</td>";
        echo "<td>" . $qty . "</td>";
        echo "<td>" . $new_stock . "</td>";
        echo "</tr>";
    } else {
        echo "<tr>";
        echo "<td>Product ID: " . $product_id . "</td>";
        echo "<td colspan='3' class='error'>Product not found in database</td>";
        echo "</tr>";
    }
}
echo "</table>";

// Verify database connection
echo "<h2>Database Connection:</h2>";
if ($conn->connect_error) {
    echo "<p class='error'>Connection Error: " . $conn->connect_error . "</p>";
} else {
    echo "<p class='success'>Connected successfully</p>";
}

// Check if products table has stock column
echo "<h2>Database Schema Check:</h2>";
$columns = $conn->query("SHOW COLUMNS FROM products LIKE 'stock'");
if ($columns && $columns->num_rows > 0) {
    echo "<p class='success'>Stock column exists</p>";
} else {
    echo "<p class='error'>Stock column does NOT exist - this is the problem!</p>";
    echo "<p>Run: ALTER TABLE products ADD COLUMN stock INT DEFAULT 10;</p>";
}

$conn->close();
?>
</body>
</html>";
?>
