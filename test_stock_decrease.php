<?php
/**
 * Test Stock Decrease Flow
 * Simulates what happens during checkout
 */

session_start();

// Simulate a logged-in user
$_SESSION['user_id'] = 1;
$_SESSION['user_email'] = 'test@example.com';

// Simulate cart items (like they would be after init_cart.php)
$_SESSION['cart'] = [
    [
        'id' => 1,
        'name' => 'Test Processor',
        'price' => 100.00,
        'image' => 'test.png',
        'quantity' => 2
    ],
    [
        'id' => 2,
        'name' => 'Test Memory',
        'price' => 50.00,
        'image' => 'test2.png',
        'quantity' => 1
    ]
];

// Simulate selected items (first item only)
$_SESSION['selected_cart_indices'] = [0];

$conn = new mysqli("localhost", "root", "", "smartsolutions");

echo "<!DOCTYPE html>
<html>
<head>
<title>Stock Decrease Test</title>
<style>
body { font-family: Arial; margin: 20px; }
.section { background: #f0f0f0; padding: 15px; margin: 15px 0; border-left: 4px solid #0062F6; }
table { border-collapse: collapse; width: 100%; }
th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
th { background: #0062F6; color: white; }
</style>
</head>
<body>
<h1>Stock Decrease Simulation Test</h1>";

// Get current stock before
echo "<div class='section'><h2>Step 1: Current Stock Before Checkout</h2>";
$result = $conn->query("SELECT id, name, stock FROM products WHERE id IN (1, 2)");
echo "<table>";
echo "<tr><th>Product ID</th><th>Name</th><th>Current Stock</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
    echo "<td><strong>" . $row['stock'] . "</strong></td>";
    echo "</tr>";
}
echo "</table></div>";

// Simulate filtering like process_checkout does
echo "<div class='section'><h2>Step 2: Selected Items for Checkout</h2>";

$cart = $_SESSION['cart'];
$selectedIndices = $_SESSION['selected_cart_indices'] ?? [];

if (!empty($selectedIndices)) {
    $filteredCart = [];
    foreach ($selectedIndices as $index) {
        if (isset($cart[$index])) {
            $item = $cart[$index];
            if (!isset($item['quantity']) || $item['quantity'] < 1) {
                $item['quantity'] = 1;
            }
            $filteredCart[] = $item;
        }
    }
    $cart = $filteredCart;
}

echo "<table>";
echo "<tr><th>Product ID</th><th>Product Name</th><th>Quantity</th><th>Action</th></tr>";
foreach ($cart as $item) {
    echo "<tr>";
    echo "<td>" . $item['id'] . "</td>";
    echo "<td>" . htmlspecialchars($item['name']) . "</td>";
    echo "<td>" . $item['quantity'] . "</td>";
    echo "<td>Will decrease by " . $item['quantity'] . "</td>";
    echo "</tr>";
}
echo "</table></div>";

// Simulate stock update
echo "<div class='section'><h2>Step 3: Stock Update Execution</h2>";
echo "<p>Executing stock decrements...</p>";

foreach ($cart as $item) {
    if (isset($item['id'])) {
        $product_id = intval($item['id']);
        $quantity = isset($item['quantity']) ? intval($item['quantity']) : 1;
        
        echo "<p>Processing Product ID $product_id, Quantity: $quantity</p>";
        
        $update_stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        if ($update_stmt) {
            $update_stmt->bind_param("ii", $quantity, $product_id);
            $result = $update_stmt->execute();
            
            if ($result) {
                echo "<p style='color: green;'><strong>✓ Stock updated for product $product_id</strong></p>";
            } else {
                echo "<p style='color: red;'><strong>✗ Failed: " . $update_stmt->error . "</strong></p>";
            }
            $update_stmt->close();
        } else {
            echo "<p style='color: red;'><strong>✗ Prepare failed: " . $conn->error . "</strong></p>";
        }
    }
}

// Check stock after
echo "</div>";
echo "<div class='section'><h2>Step 4: Stock After Checkout</h2>";
$result = $conn->query("SELECT id, name, stock FROM products WHERE id IN (1, 2)");
echo "<table>";
echo "<tr><th>Product ID</th><th>Name</th><th>New Stock</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
    echo "<td><strong>" . $row['stock'] . "</strong></td>";
    echo "</tr>";
}
echo "</table></div>";

// Restore original values for testing again
echo "<div class='section'><h2>Restoring Original Values</h2>";
$conn->query("UPDATE products SET stock = 10 WHERE id IN (1, 2)");
echo "<p>Stock values restored to 10 for testing</p>";
echo "</div>";

$conn->close();

echo "</body></html>";
?>
