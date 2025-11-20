<?php
/**
 * Cart Database Verification & Debugging Script
 * This script helps verify that the cart is being saved to the database
 */

session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("❌ Database Connection Failed: " . $conn->connect_error);
}

echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "<title>Cart Database Verification</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }";
echo ".success { background: #d4edda; border: 1px solid #c3e6cb; padding: 10px; margin: 10px 0; border-radius: 5px; color: #155724; }";
echo ".error { background: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; margin: 10px 0; border-radius: 5px; color: #721c24; }";
echo ".info { background: #d1ecf1; border: 1px solid #bee5eb; padding: 10px; margin: 10px 0; border-radius: 5px; color: #0c5460; }";
echo ".warning { background: #fff3cd; border: 1px solid #ffeeba; padding: 10px; margin: 10px 0; border-radius: 5px; color: #856404; }";
echo "h1 { color: #333; }";
echo "h2 { color: #666; margin-top: 30px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }";
echo "table { border-collapse: collapse; width: 100%; background: white; }";
echo "table th, table td { border: 1px solid #ddd; padding: 10px; text-align: left; }";
echo "table th { background: #f9f9f9; font-weight: bold; }";
echo ".code { background: #f4f4f4; padding: 10px; border-radius: 5px; font-family: monospace; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<h1>🔍 Cart Database Verification & Debugging</h1>";

// Step 1: Check if user is logged in
echo "<h2>1. Session Status</h2>";
if (isset($_SESSION['user_id'])) {
    echo "<div class='success'>";
    echo "✅ User is logged in<br>";
    echo "User ID: " . $_SESSION['user_id'] . "<br>";
    echo "This means cart WILL be saved to database!";
    echo "</div>";
} else {
    echo "<div class='warning'>";
    echo "⚠️ User is NOT logged in<br>";
    echo "Cart will ONLY be saved in session, not database!<br>";
    echo "<strong>Solution:</strong> Login first: <a href='login.php'>Go to Login</a>";
    echo "</div>";
}

// Step 2: Check if tables exist
echo "<h2>2. Database Tables Status</h2>";

// Check shopping_cart table
$tableCheckQuery = "SHOW TABLES LIKE 'shopping_cart'";
$tableCheckResult = $conn->query($tableCheckQuery);
if ($tableCheckResult && $tableCheckResult->num_rows > 0) {
    echo "<div class='success'>✅ shopping_cart table EXISTS</div>";
} else {
    echo "<div class='error'>❌ shopping_cart table MISSING</div>";
    echo "<div class='info'>Creating table now...</div>";
    $createTableQuery = "CREATE TABLE IF NOT EXISTS `shopping_cart` (
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
        echo "<div class='success'>✅ shopping_cart table created successfully!</div>";
    } else {
        echo "<div class='error'>❌ Error creating shopping_cart table: " . $conn->error . "</div>";
    }
}

// Check products table
$tableCheckQuery2 = "SHOW TABLES LIKE 'products'";
$tableCheckResult2 = $conn->query($tableCheckQuery2);
if ($tableCheckResult2 && $tableCheckResult2->num_rows > 0) {
    echo "<div class='success'>✅ products table EXISTS</div>";
} else {
    echo "<div class='error'>❌ products table MISSING</div>";
    echo "<div class='info'>Note: products table is optional since products are stored in PHP arrays</div>";
}

// Step 3: Show current cart data
echo "<h2>3. Session Cart Data</h2>";
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    echo "<div class='success'>✅ Session cart has " . count($_SESSION['cart']) . " item(s)</div>";
    echo "<table>";
    echo "<tr><th>Product ID</th><th>Product Name</th><th>Quantity</th><th>Price</th><th>Subtotal</th></tr>";
    $sessionTotal = 0;
    foreach ($_SESSION['cart'] as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $sessionTotal += $subtotal;
        echo "<tr>";
        echo "<td>" . $item['id'] . "</td>";
        echo "<td>" . $item['name'] . "</td>";
        echo "<td>" . $item['quantity'] . "</td>";
        echo "<td>₱" . number_format($item['price'], 2) . "</td>";
        echo "<td>₱" . number_format($subtotal, 2) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p><strong>Session Cart Total: ₱" . number_format($sessionTotal, 2) . "</strong></p>";
} else {
    echo "<div class='info'>ℹ️ Session cart is empty</div>";
}

// Step 4: Show database cart data (if user logged in)
echo "<h2>4. Database Cart Data</h2>";
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Create table if it doesn't exist
    $createTableQuery = "CREATE TABLE IF NOT EXISTS `shopping_cart` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` INT UNSIGNED NOT NULL,
      `product_id` INT UNSIGNED NOT NULL,
      `quantity` INT NOT NULL DEFAULT 1,
      `added_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `user_product` (`user_id`, `product_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    $conn->query($createTableQuery);
    
    // Query database cart
    $dbCartQuery = "SELECT * FROM shopping_cart WHERE user_id = ? ORDER BY added_at DESC";
    $dbStmt = $conn->prepare($dbCartQuery);
    $dbStmt->bind_param("i", $user_id);
    $dbStmt->execute();
    $dbResult = $dbStmt->get_result();
    
    if ($dbResult->num_rows > 0) {
        echo "<div class='success'>✅ Database cart has " . $dbResult->num_rows . " item(s) for user #" . $user_id . "</div>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Product ID</th><th>Quantity</th><th>Added At</th><th>Updated At</th></tr>";
        $dbTotal = 0;
        while ($row = $dbResult->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['product_id'] . "</td>";
            echo "<td>" . $row['quantity'] . "</td>";
            echo "<td>" . $row['added_at'] . "</td>";
            echo "<td>" . $row['updated_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='warning'>⚠️ Database cart is EMPTY for user #" . $user_id . "</div>";
        echo "<div class='info'>This is normal if you haven't added any products yet while logged in</div>";
    }
    $dbStmt->close();
} else {
    echo "<div class='warning'>⚠️ You are not logged in, so database cart cannot be checked</div>";
    echo "<div class='info'><a href='login.php'>Login here</a> to see your database cart</div>";
}

// Step 5: File status
echo "<h2>5. Required Files Status</h2>";
$files = [
    'add_to_cart.php' => 'Handles adding products to cart and saving to database',
    'cart.php' => 'Displays cart and loads from database',
    'update_cart_quantity.php' => 'Updates quantity and syncs with database',
    'ajax-cart.js' => 'Frontend button interactions'
];

foreach ($files as $filename => $description) {
    $filepath = dirname(__FILE__) . '/' . $filename;
    if (file_exists($filepath)) {
        echo "<div class='success'>✅ $filename - $description</div>";
    } else {
        echo "<div class='error'>❌ $filename - MISSING!</div>";
    }
}

// Step 6: How the cart save process works
echo "<h2>6. How Cart Save Works</h2>";
echo "<div class='info'>";
echo "<strong>When you add a product to cart:</strong><br>";
echo "1. Click 'Add to Cart' button<br>";
echo "2. ajax-cart.js captures product details<br>";
echo "3. Sends POST request to add_to_cart.php<br>";
echo "4. add_to_cart.php:<br>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;a) Adds to SESSION cart (immediate display)<br>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;b) If you're logged in: SAVES TO DATABASE<br>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;c) If product already in cart: INCREASES quantity<br>";
echo "5. JavaScript shows 'Added to cart' message<br>";
echo "</div>";

// Step 7: Test queries
echo "<h2>7. Direct Database Test</h2>";
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $testQuery = "SELECT COUNT(*) as count FROM shopping_cart WHERE user_id = ?";
    $testStmt = $conn->prepare($testQuery);
    $testStmt->bind_param("i", $user_id);
    $testStmt->execute();
    $testResult = $testStmt->get_result();
    $testRow = $testResult->fetch_assoc();
    
    echo "<div class='info'>";
    echo "User #" . $user_id . " has " . $testRow['count'] . " items in database cart<br>";
    echo "<strong>SQL Query:</strong><br>";
    echo "<div class='code'>SELECT * FROM shopping_cart WHERE user_id = " . $user_id . ";</div>";
    echo "</div>";
    $testStmt->close();
} else {
    echo "<div class='warning'>Login required to test</div>";
}

// Step 8: Instructions
echo "<h2>8. Instructions to Test Cart Save</h2>";
echo "<div class='info'>";
echo "<strong>Step 1:</strong> <a href='register.php'>Register an account</a><br>";
echo "<strong>Step 2:</strong> <a href='login.php'>Login to your account</a><br>";
echo "<strong>Step 3:</strong> Go to any product page and add products to cart<br>";
echo "<strong>Step 4:</strong> <a href='logout.php'>Logout from your account</a><br>";
echo "<strong>Step 5:</strong> <a href='login.php'>Login again</a> with the same account<br>";
echo "<strong>Step 6:</strong> Go to <a href='cart.php'>cart page</a><br>";
echo "<strong>Result:</strong> Your products should STILL BE THERE ✅<br>";
echo "</div>";

$conn->close();

echo "</body>";
echo "</html>";
?>
