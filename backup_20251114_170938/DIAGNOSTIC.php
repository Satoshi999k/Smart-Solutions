<?php
/**
 * FULL DIAGNOSTIC - Debug why cart isn't saving to database
 */
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html>
<head>
<title>Cart Save Diagnostic</title>
<style>
body { font-family: Arial; margin: 20px; background: #f5f5f5; }
h1 { color: #333; }
h2 { color: #0066cc; margin-top: 30px; }
.success { background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #28a745; }
.error { background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #dc3545; }
.warning { background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #ffc107; }
.info { background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #0c5460; }
table { border-collapse: collapse; width: 100%; background: white; margin: 10px 0; }
table th, table td { padding: 12px; border: 1px solid #ddd; text-align: left; }
table th { background: #f0f0f0; }
code { background: #f0f0f0; padding: 5px; border-radius: 3px; font-family: monospace; }
li { margin: 8px 0; }
.button-link { display: inline-block; padding: 10px 15px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px 5px 5px 0; }
.button-link:hover { background: #0056b3; }
</style>
</head>
<body>";

// TEST 1: Database Connection
echo "<h2>TEST 1: Database Connection</h2>";
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    echo '<div class="error">❌ DATABASE CONNECTION FAILED</div>';
    echo '<div class="error">' . htmlspecialchars($conn->connect_error) . '</div>';
    die();
} else {
    echo '<div class="success">✅ Database connected successfully</div>';
}

// TEST 2: Check Login Status
echo "<h2>TEST 2: Login Status</h2>";
if (!isset($_SESSION['user_id'])) {
    echo '<div class="error">❌ NOT LOGGED IN</div>';
    echo '<div class="warning">Products only save to database when logged in!</div>';
    echo '<p><a class="button-link" href="login.php">Go to Login</a></p>';
    die();
} else {
    $user_id = $_SESSION['user_id'];
    echo '<div class="success">✅ Logged in as User ID: ' . htmlspecialchars($user_id) . '</div>';
}

// TEST 3: Check shopping_cart table
echo "<h2>TEST 3: Shopping Cart Table</h2>";
$tableCheck = $conn->query("SHOW TABLES LIKE 'shopping_cart'");
if (!$tableCheck || $tableCheck->num_rows == 0) {
    echo '<div class="warning">⚠️ shopping_cart table does NOT exist</div>';
    echo '<p>Attempting to create table...</p>';
    
    $createQuery = "CREATE TABLE IF NOT EXISTS `shopping_cart` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` INT UNSIGNED NOT NULL,
      `product_id` INT UNSIGNED NOT NULL,
      `quantity` INT NOT NULL DEFAULT 1,
      `added_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `user_product` (`user_id`, `product_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conn->query($createQuery)) {
        echo '<div class="success">✅ shopping_cart table created successfully</div>';
    } else {
        echo '<div class="error">❌ Failed to create table: ' . htmlspecialchars($conn->error) . '</div>';
    }
} else {
    echo '<div class="success">✅ shopping_cart table exists</div>';
}

// TEST 4: Check files
echo "<h2>TEST 4: Required Files</h2>";
$files = [
    'ajax-cart.js' => 'JavaScript for handling button clicks',
    'add_to_cart.php' => 'Backend that saves to database',
    'cart.php' => 'Cart display page'
];

$allFilesExist = true;
foreach ($files as $file => $desc) {
    if (file_exists($file)) {
        echo '<div class="success">✅ ' . $file . ' - ' . $desc . '</div>';
    } else {
        echo '<div class="error">❌ ' . $file . ' - MISSING! This is required!</div>';
        $allFilesExist = false;
    }
}

// TEST 5: Check add_to_cart.php code
echo "<h2>TEST 5: Verify add_to_cart.php Logic</h2>";
$addCartCode = file_get_contents('add_to_cart.php');

$checks = [
    'Database save' => strpos($addCartCode, 'shopping_cart') !== false,
    'Session check' => strpos($addCartCode, '$_SESSION[\'user_id\']') !== false,
    'CREATE TABLE' => strpos($addCartCode, 'CREATE TABLE') !== false,
    'INSERT/UPDATE' => (strpos($addCartCode, 'INSERT INTO') !== false || strpos($addCartCode, 'UPDATE')) !== false,
];

foreach ($checks as $check => $result) {
    if ($result) {
        echo '<div class="success">✅ ' . $check . ' - Found in code</div>';
    } else {
        echo '<div class="error">❌ ' . $check . ' - NOT found in code!</div>';
    }
}

// TEST 6: Database content
echo "<h2>TEST 6: Current Database Content</h2>";
echo '<div class="info">Items in shopping_cart table for User ID ' . $user_id . ':</div>';

$result = $conn->query("SELECT * FROM shopping_cart WHERE user_id = $user_id ORDER BY added_at DESC");
if ($result && $result->num_rows > 0) {
    echo '<div class="success">✅ Found ' . $result->num_rows . ' item(s):</div>';
    echo '<table>';
    echo '<tr><th>ID</th><th>Product ID</th><th>Quantity</th><th>Added</th><th>Updated</th></tr>';
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . $row['product_id'] . '</td>';
        echo '<td>' . $row['quantity'] . '</td>';
        echo '<td>' . $row['added_at'] . '</td>';
        echo '<td>' . $row['updated_at'] . '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo '<div class="warning">⚠️ No items found in database for this user</div>';
}

// TEST 7: Session cart
echo "<h2>TEST 7: Session Cart Content</h2>";
if (isset($_SESSION['cart']) && is_array($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    echo '<div class="success">✅ Session cart has ' . count($_SESSION['cart']) . ' item(s):</div>';
    echo '<ul>';
    foreach ($_SESSION['cart'] as $item) {
        echo '<li>Product ID ' . $item['id'] . ': ' . htmlspecialchars($item['name']) . ' (Qty: ' . $item['quantity'] . ')</li>';
    }
    echo '</ul>';
    echo '<div class="warning">If items are in SESSION but NOT in DATABASE, the database save code is not executing!</div>';
} else {
    echo '<div class="info">Session cart is empty</div>';
}

// TEST 8: Instructions
echo "<h2>TEST 8: What to Do Next</h2>";
echo '<div class="info">
<h3>Follow these steps:</h3>
<ol>
<li>Go to <a href="products/motherboard.php">motherboard.php</a></li>
<li>Click the <strong>BLUE cart button</strong> (with cart icon 🛒) on any product</li>
<li>A popup will ask for quantity - enter a number and click ADD</li>
<li>You should see a "✅ Added to cart" notification</li>
<li>Then come back to this page and <strong>REFRESH IT</strong></li>
<li>Check if the product now appears in "<strong>TEST 6: Current Database Content</strong>" above</li>
</ol>
</div>';

// TEST 9: Add test product manually
echo "<h2>TEST 9: Manual Test Insert</h2>";
echo '<form method="post">';
echo '<input type="hidden" name="test_insert" value="1">';
echo '<button type="submit" style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">Test: Add Product 23 to Database</button>';
echo '</form>';

if (isset($_POST['test_insert']) && $_POST['test_insert'] == '1') {
    $test_product_id = 23;
    $test_quantity = 1;
    
    $insertQuery = "INSERT INTO shopping_cart (user_id, product_id, quantity, added_at, updated_at) 
                    VALUES (?, ?, ?, NOW(), NOW())
                    ON DUPLICATE KEY UPDATE quantity = quantity + ?, updated_at = NOW()";
    
    $stmt = $conn->prepare($insertQuery);
    if (!$stmt) {
        echo '<div class="error">❌ Prepare failed: ' . htmlspecialchars($conn->error) . '</div>';
    } else {
        $stmt->bind_param("iiii", $user_id, $test_product_id, $test_quantity, $test_quantity);
        if ($stmt->execute()) {
            echo '<div class="success">✅ Test product inserted into database!</div>';
            echo '<p>Refresh this page to see it in TEST 6 above.</p>';
        } else {
            echo '<div class="error">❌ Execute failed: ' . htmlspecialchars($stmt->error) . '</div>';
        }
        $stmt->close();
    }
}

// TEST 10: Clear test data
echo "<h2>TEST 10: Clean Up Test Data</h2>";
echo '<form method="post">';
echo '<input type="hidden" name="clear_all" value="1">';
echo '<button type="submit" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;" onclick="return confirm(\'Clear all items for this user?\')">Clear All Cart Items</button>';
echo '</form>';

if (isset($_POST['clear_all']) && $_POST['clear_all'] == '1') {
    $conn->query("DELETE FROM shopping_cart WHERE user_id = $user_id");
    echo '<div class="success">✅ All cart items cleared for user ' . $user_id . '</div>';
}

echo '<hr>';
echo '<p><a class="button-link" href="DIAGNOSTIC.php">🔄 Refresh Diagnostic</a>';
echo '<a class="button-link" href="products/motherboard.php">Go to Motherboard</a>';
echo '<a class="button-link" href="cart.php">View Cart</a></p>';

$conn->close();
echo '</body></html>';
?>
