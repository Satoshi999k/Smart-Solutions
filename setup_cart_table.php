<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

$sql = "CREATE TABLE IF NOT EXISTS `shopping_cart` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `added_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_product` (`user_id`, `product_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

if ($conn->query($sql) === TRUE) {
    echo "<h2 style='color: green;'>✅ Success!</h2>";
    echo "<p>The <strong>shopping_cart</strong> table has been created successfully.</p>";
    echo "<p>Your cart will now persist across logout/login sessions!</p>";
    echo "<hr>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li>Login to your account</li>";
    echo "<li>Add products to cart</li>";
    echo "<li>Logout</li>";
    echo "<li>Login again - your cart will still be there! ✅</li>";
    echo "</ol>";
    echo "<p style='color: #666; margin-top: 30px;'>You can now safely delete this file.</p>";
} else {
    echo "<h2 style='color: red;'>❌ Error</h2>";
    echo "<p>Error creating table: " . $conn->error . "</p>";
}

$conn->close();
?>
