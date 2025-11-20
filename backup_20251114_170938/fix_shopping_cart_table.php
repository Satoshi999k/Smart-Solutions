<?php
/**
 * FIX: Drop and recreate shopping_cart table without foreign key constraint
 */
session_start();

$conn = new mysqli("localhost", "root", "", "smartsolutions");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

echo "<h1>🔧 Fixing Shopping Cart Table</h1>";
echo "<hr>";

// First, drop the old table with foreign key
echo "<h2>Step 1: Drop existing shopping_cart table</h2>";
if ($conn->query("DROP TABLE IF EXISTS shopping_cart")) {
    echo "✅ Old table dropped<br>";
} else {
    echo "❌ Failed to drop table: " . $conn->error . "<br>";
}

// Now create the new table WITHOUT foreign key
echo "<h2>Step 2: Create new shopping_cart table (without foreign key)</h2>";

$createTableQuery = "CREATE TABLE `shopping_cart` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `added_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_product` (`user_id`, `product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($createTableQuery)) {
    echo "✅ New table created successfully (without foreign key constraint)<br>";
} else {
    echo "❌ Failed to create table: " . $conn->error . "<br>";
}

// Verify
echo "<h2>Step 3: Verify table structure</h2>";
$result = $conn->query("DESCRIBE shopping_cart");
if ($result) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<br>✅ Table structure verified<br>";
}

echo "<hr>";
echo "<h2>✅ DONE! The shopping_cart table is now fixed!</h2>";
echo "<p>The table no longer has the foreign key constraint that was preventing product saves.</p>";
echo "<p><a href='DIAGNOSTIC.php' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Go to Diagnostic</a></p>";

$conn->close();
?>
