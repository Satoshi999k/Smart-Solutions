<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Stock Update Debug</h2>";

// Simulate what happens in checkout
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

echo "<h3>Current Cart Contents:</h3>";
echo "<pre>";
print_r($cart);
echo "</pre>";

echo "<h3>What will be updated:</h3>";

if (!empty($cart)) {
    foreach ($cart as $item) {
        if (isset($item['id'])) {
            $product_id = intval($item['id']);
            $quantity = isset($item['quantity']) ? intval($item['quantity']) : 1;
            
            // Get current stock
            $stock_query = "SELECT id, name, stock FROM products WHERE id = $product_id";
            $stock_result = $conn->query($stock_query);
            $stock_row = $stock_result->fetch_assoc();
            
            echo "Product ID: " . $product_id . "<br>";
            echo "Product Name: " . htmlspecialchars($stock_row['name']) . "<br>";
            echo "Current Stock: " . $stock_row['stock'] . "<br>";
            echo "Quantity to Buy: " . $quantity . "<br>";
            echo "New Stock Will Be: " . ($stock_row['stock'] - $quantity) . "<br>";
            echo "Query: UPDATE products SET stock = stock - $quantity WHERE id = $product_id<br>";
            echo "---<br>";
        }
    }
} else {
    echo "Cart is empty!";
}

echo "<h3>Products Table Current State (First 5):</h3>";
$result = $conn->query("SELECT id, name, stock FROM products LIMIT 5");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Name</th><th>Stock</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
    echo "<td>" . $row['stock'] . "</td>";
    echo "</tr>";
}
echo "</table>";

$conn->close();
?>
