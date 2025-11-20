<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

echo "<h2>🔍 Database Diagnostic Report</h2>";
echo "<hr>";

// Check if shopping_cart table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'shopping_cart'");
if ($tableCheck && $tableCheck->num_rows > 0) {
    echo "<p style='color: green;'>✅ shopping_cart table EXISTS</p>";
    
    // Get table structure
    $structureResult = $conn->query("DESCRIBE shopping_cart");
    echo "<h3>Table Structure:</h3>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $structureResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Count records
    $countResult = $conn->query("SELECT COUNT(*) as total FROM shopping_cart");
    $countRow = $countResult->fetch_assoc();
    echo "<p><strong>Total records in shopping_cart: " . $countRow['total'] . "</strong></p>";
    
    // Show some records
    if ($countRow['total'] > 0) {
        echo "<h3>Sample Records:</h3>";
        $sampleResult = $conn->query("SELECT * FROM shopping_cart LIMIT 5");
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>User ID</th><th>Product ID</th><th>Quantity</th><th>Added At</th><th>Updated At</th></tr>";
        while ($row = $sampleResult->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['user_id'] . "</td>";
            echo "<td>" . $row['product_id'] . "</td>";
            echo "<td>" . $row['quantity'] . "</td>";
            echo "<td>" . $row['added_at'] . "</td>";
            echo "<td>" . $row['updated_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<p style='color: red;'>❌ shopping_cart table DOES NOT EXIST</p>";
    echo "<p>Creating table now...</p>";
    
    $createSQL = "CREATE TABLE IF NOT EXISTS `shopping_cart` (
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
    
    if ($conn->query($createSQL) === TRUE) {
        echo "<p style='color: green;'>✅ shopping_cart table created successfully!</p>";
    } else {
        echo "<p style='color: red;'>❌ Error creating table: " . $conn->error . "</p>";
    }
}

echo "<hr>";

// Check users table
$usersCheck = $conn->query("SHOW TABLES LIKE 'users'");
if ($usersCheck && $usersCheck->num_rows > 0) {
    echo "<p style='color: green;'>✅ users table EXISTS</p>";
    $userCount = $conn->query("SELECT COUNT(*) as total FROM users");
    $userRow = $userCount->fetch_assoc();
    echo "<p>Total users: " . $userRow['total'] . "</p>";
} else {
    echo "<p style='color: red;'>❌ users table DOES NOT EXIST</p>";
}

// Check products table
$productsCheck = $conn->query("SHOW TABLES LIKE 'products'");
if ($productsCheck && $productsCheck->num_rows > 0) {
    echo "<p style='color: green;'>✅ products table EXISTS</p>";
    $productCount = $conn->query("SELECT COUNT(*) as total FROM products");
    $productRow = $productCount->fetch_assoc();
    echo "<p>Total products: " . $productRow['total'] . "</p>";
} else {
    echo "<p style='color: red;'>❌ products table DOES NOT EXIST</p>";
}

echo "<hr>";
echo "<p><a href='http://localhost/ITP122/'>Back to Home</a></p>";

$conn->close();
?>
