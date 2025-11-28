<?php
$conn = new mysqli("localhost", "root", "", "smartsolutions");

echo "<h2>Shopping Cart Table Contents</h2>";
echo "<hr>";

// Show ALL items in shopping_cart table
$result = $conn->query("SELECT sc.id, sc.user_id, sc.product_id, sc.quantity, p.name, p.category FROM shopping_cart sc LEFT JOIN products p ON sc.product_id = p.id ORDER BY sc.user_id, sc.product_id");

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Cart ID</th><th>User ID</th><th>Product ID</th><th>Product Name</th><th>Category</th><th>Quantity</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['user_id']}</td>";
        echo "<td>{$row['product_id']}</td>";
        echo "<td>{$row['name']}</td>";
        echo "<td>{$row['category']}</td>";
        echo "<td>{$row['quantity']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Shopping cart table is empty";
}

echo "<hr>";

// Check product IDs 144-148 (headsets from old hardcoded array)
echo "<h3>Checking if old headset IDs (144-148) are being used:</h3>";
$headset_result = $conn->query("SELECT id, name, category FROM products WHERE id BETWEEN 144 AND 148");
if ($headset_result) {
    while ($row = $headset_result->fetch_assoc()) {
        echo "ID {$row['id']}: {$row['name']} (Category: {$row['category']})<br>";
    }
}

$conn->close();
?>
