<?php
/**
 * QUICK TEST - Are products being saved to the database?
 */
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "smartsolutions");
if ($conn->connect_error) die("<h2>❌ Database Error: " . $conn->connect_error . "</h2>");

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    echo "<h1>❌ NOT LOGGED IN</h1>";
    echo "<p>Please <a href='login.php'>login first</a> before testing cart save.</p>";
    echo "<p>The cart only saves to database for logged-in users.</p>";
    exit;
}

$user_id = $_SESSION['user_id'];
echo "<h1>✅ Testing Cart Save System</h1>";
echo "<p>Logged in as User ID: <strong>$user_id</strong></p>";
echo "<hr>";

// Create tables if they don't exist
$conn->query("CREATE TABLE IF NOT EXISTS `shopping_cart` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `added_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_product` (`user_id`, `product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

echo "<h2>1️⃣ SESSION CART (Temporary - in your browser)</h2>";
if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    echo "<p>✅ Found " . count($_SESSION['cart']) . " item(s)</p>";
    foreach ($_SESSION['cart'] as $item) {
        echo "<li>Product ID {$item['id']}: {$item['name']} (Qty: {$item['quantity']})</li>";
    }
} else {
    echo "<p>⚠️ Session cart is empty. Add a product first!</p>";
}

echo "<h2>2️⃣ DATABASE CART (Permanent - persists after logout)</h2>";
$result = $conn->query("SELECT * FROM shopping_cart WHERE user_id = $user_id");
if ($result && $result->num_rows > 0) {
    echo "<p>✅ Found " . $result->num_rows . " item(s) in database!</p>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr style='background: #f0f0f0;'><th>Product ID</th><th>Quantity</th><th>Added</th><th>Updated</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['product_id']}</td>";
        echo "<td>{$row['quantity']}</td>";
        echo "<td>{$row['added_at']}</td>";
        echo "<td>{$row['updated_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>⚠️ Database cart is empty. Items haven't been saved yet.</p>";
}

echo "<h2>3️⃣ INSTRUCTIONS TO TEST</h2>";
echo "<ol>";
echo "<li>Go to <a href='motherboard.php'><strong>motherboard.php</strong></a></li>";
echo "<li>Find a product and click the <strong>BLUE cart button</strong> (not gray BUY NOW)</li>";
echo "<li>Select quantity and click ADD</li>";
echo "<li>Wait for 'Added to cart' notification</li>";
echo "<li>Come back here and refresh this page</li>";
echo "<li>You should see the product in 'DATABASE CART' above</li>";
echo "<li>Then <a href='logout.php'>logout</a> and <a href='login.php'>login again</a></li>";
echo "<li>The product should STILL be in the database cart!</li>";
echo "</ol>";

echo "<hr>";
echo "<a href='QUICK_TEST.php' style='padding: 10px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>🔄 Refresh Test</a> ";
echo "<a href='motherboard.php' style='padding: 10px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;'>Go to Motherboard</a> ";
echo "<a href='cart.php' style='padding: 10px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;'>View Full Cart</a>";

$conn->close();
?>
