<?php
$conn = new mysqli("localhost", "root", "", "smartsolutions");

echo "<h2>Clearing Shopping Cart</h2>";
echo "<hr>";

// Delete all items from shopping_cart
if ($conn->query("TRUNCATE TABLE shopping_cart")) {
    echo "✓ Shopping cart table cleared successfully<br>";
} else {
    echo "Error clearing shopping cart: " . $conn->error;
}

// Also clear session cart
session_start();
$_SESSION['cart'] = [];
echo "✓ Session cart cleared<br>";

echo "<hr>";
echo "<p><strong>Cart is now empty!</strong></p>";
echo "<p><a href='products/laptop.php'>Go to Laptop page</a> and try adding items again.</p>";

$conn->close();
?>
