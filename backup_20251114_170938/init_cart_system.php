<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("<h2 style='color: red;'>❌ Database Connection Failed</h2><p>" . $conn->connect_error . "</p>");
}

echo "<h2>🔧 SmartSolutions Cart System Setup</h2>";
echo "<hr>";

// 1. Check if shopping_cart table exists, if not create it
$tableCheck = $conn->query("SHOW TABLES LIKE 'shopping_cart'");

if (!$tableCheck || $tableCheck->num_rows === 0) {
    echo "<p>⚙️ Creating shopping_cart table...</p>";
    
    $createTableSQL = "CREATE TABLE IF NOT EXISTS `shopping_cart` (
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
    
    if ($conn->query($createTableSQL) === TRUE) {
        echo "<p style='color: green;'>✅ shopping_cart table created successfully!</p>";
    } else {
        echo "<p style='color: red;'>❌ Error creating shopping_cart table: " . $conn->error . "</p>";
    }
} else {
    echo "<p style='color: green;'>✅ shopping_cart table already exists</p>";
}

echo "<hr>";

// 2. Verify table structure
echo "<h3>📋 Table Structure Verification</h3>";
$describeResult = $conn->query("DESCRIBE shopping_cart");

if ($describeResult) {
    echo "<table border='1' cellpadding='8' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr style='background-color: #f0f0f0;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    
    while ($row = $describeResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td><strong>" . htmlspecialchars($row['Field']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Could not verify table structure: " . $conn->error . "</p>";
}

echo "<hr>";

// 3. Check data
echo "<h3>📊 Database Statistics</h3>";

$countUsers = $conn->query("SELECT COUNT(*) as total FROM users");
$countProducts = $conn->query("SELECT COUNT(*) as total FROM products");
$countCart = $conn->query("SELECT COUNT(*) as total FROM shopping_cart");

if ($countUsers) {
    $row = $countUsers->fetch_assoc();
    echo "<p>👥 Users: " . $row['total'] . "</p>";
}

if ($countProducts) {
    $row = $countProducts->fetch_assoc();
    echo "<p>📦 Products: " . $row['total'] . "</p>";
}

if ($countCart) {
    $row = $countCart->fetch_assoc();
    echo "<p>🛒 Cart Items: " . $row['total'] . "</p>";
}

echo "<hr>";

// 4. Show sample cart data if exists
echo "<h3>🛒 Sample Cart Data</h3>";
$sampleCart = $conn->query("SELECT sc.*, p.name as product_name, u.email as user_email 
                             FROM shopping_cart sc
                             LEFT JOIN products p ON sc.product_id = p.id
                             LEFT JOIN users u ON sc.user_id = u.id
                             LIMIT 10");

if ($sampleCart && $sampleCart->num_rows > 0) {
    echo "<table border='1' cellpadding='8' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr style='background-color: #f0f0f0;'><th>ID</th><th>User Email</th><th>Product ID</th><th>Product Name</th><th>Quantity</th><th>Added</th></tr>";
    
    while ($row = $sampleCart->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['user_email'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($row['product_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['product_name'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
        echo "<td>" . htmlspecialchars($row['added_at']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: #999;'>No cart items yet. Start by adding products to your cart!</p>";
}

echo "<hr>";

// 5. Next steps
echo "<h3>✅ Next Steps</h3>";
echo "<ol>";
echo "<li><a href='http://localhost/ITP122/'>Go to Home Page</a></li>";
echo "<li>Login to your account</li>";
echo "<li>Add products to your cart</li>";
echo "<li>Logout</li>";
echo "<li>Login again to verify your cart items are still there!</li>";
echo "</ol>";

echo "<p style='margin-top: 20px; text-align: center;'>";
echo "<a href='http://localhost/ITP122/' style='padding: 10px 20px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px;'>← Back to Home</a>";
echo "</p>";

$conn->close();
?>
