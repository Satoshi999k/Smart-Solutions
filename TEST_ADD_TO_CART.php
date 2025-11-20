<?php
/**
 * Test add_to_cart.php response and database save
 * This script simulates what happens when the blue button is clicked
 */
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("<h1>❌ Please login first!</h1><p><a href='login.php'>Go to login</a></p>");
}

$user_id = $_SESSION['user_id'];
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("<h1>❌ Database error: " . $conn->connect_error . "</h1>");
}

echo "<h1>Testing add_to_cart.php Response</h1>";
echo "<p>Logged in as User ID: <strong>$user_id</strong></p>";
echo "<hr>";

// Simulate the POST request that happens when user clicks blue button
echo "<h2>Simulating Blue Button Click</h2>";
echo "<p>Simulating: User clicks blue cart button on motherboard (Product ID 23)</p>";

$postData = array(
    'product_id' => 23,
    'product_name' => 'MSI A520m-A Pro AMD Am4 Ddr4 Micro-ATX PCB Gaming Motherboard',
    'product_price' => 3899.00,
    'product_image' => 'image/MSI_A520m.png',
    'quantity' => 1
);

echo "<p>POST data being sent:</p>";
echo "<pre>";
print_r($postData);
echo "</pre>";

// Check if add_to_cart.php exists and is readable
if (!file_exists('add_to_cart.php')) {
    echo '<div style="background: #f8d7da; padding: 15px; border-radius: 5px; border: 1px solid #dc3545; margin: 10px 0;">';
    echo '❌ ERROR: add_to_cart.php does not exist!';
    echo '</div>';
    die();
}

// Now let's manually execute what add_to_cart.php does
echo "<h2>Step-by-Step Execution</h2>";

// Step 1: Create tables
echo "<h3>Step 1: Creating tables</h3>";

$createProductsTableQuery = "CREATE TABLE IF NOT EXISTS `products` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `image` VARCHAR(255) DEFAULT NULL,
  `category` VARCHAR(100) DEFAULT NULL,
  `stock` INT DEFAULT 0,
  `description` TEXT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($createProductsTableQuery)) {
    echo "✅ products table OK<br>";
} else {
    echo "❌ products table error: " . $conn->error . "<br>";
}

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
    echo "✅ shopping_cart table OK<br>";
} else {
    echo "❌ shopping_cart table error: " . $conn->error . "<br>";
}

// Step 2: Add to session
echo "<h3>Step 2: Adding to session cart</h3>";
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

$product_id = $postData['product_id'];
$product_name = $postData['product_name'];
$product_price = $postData['product_price'];
$product_image = $postData['product_image'];
$quantity = $postData['quantity'];

// Check if already in cart
$found = false;
for ($i = 0; $i < count($_SESSION['cart']); $i++) {
    if ($_SESSION['cart'][$i]['id'] == $product_id) {
        $_SESSION['cart'][$i]['quantity'] += $quantity;
        $found = true;
        echo "✅ Product already in session, quantity updated<br>";
        break;
    }
}

if (!$found) {
    $_SESSION['cart'][] = array(
        'id' => $product_id,
        'name' => $product_name,
        'price' => $product_price,
        'image' => $product_image,
        'quantity' => $quantity
    );
    echo "✅ Product added to session<br>";
}

// Step 3: Save to database
echo "<h3>Step 3: Saving to database</h3>";

// Check if product already in cart
$checkQuery = "SELECT id, quantity FROM shopping_cart WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($checkQuery);

if (!$stmt) {
    echo "❌ Prepare failed: " . $conn->error . "<br>";
} else {
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update quantity
        echo "✅ Product exists in database, updating quantity...<br>";
        $row = $result->fetch_assoc();
        $newQuantity = $row['quantity'] + $quantity;
        $updateQuery = "UPDATE shopping_cart SET quantity = ?, updated_at = NOW() WHERE user_id = ? AND product_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        if ($updateStmt) {
            $updateStmt->bind_param("iii", $newQuantity, $user_id, $product_id);
            if ($updateStmt->execute()) {
                echo "✅ Quantity updated in database (new qty: $newQuantity)<br>";
            } else {
                echo "❌ Update failed: " . $updateStmt->error . "<br>";
            }
            $updateStmt->close();
        } else {
            echo "❌ Prepare update failed: " . $conn->error . "<br>";
        }
    } else {
        // Insert new
        echo "✅ Product not in cart, inserting new...<br>";
        $insertQuery = "INSERT INTO shopping_cart (user_id, product_id, quantity, added_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())";
        $insertStmt = $conn->prepare($insertQuery);
        if ($insertStmt) {
            $insertStmt->bind_param("iii", $user_id, $product_id, $quantity);
            if ($insertStmt->execute()) {
                echo "✅ Product inserted into database<br>";
                echo "   Insert ID: " . $insertStmt->insert_id . "<br>";
            } else {
                echo "❌ Insert failed: " . $insertStmt->error . "<br>";
            }
            $insertStmt->close();
        } else {
            echo "❌ Prepare insert failed: " . $conn->error . "<br>";
        }
    }
    
    $stmt->close();
}

// Step 4: Verify
echo "<h3>Step 4: Verification - Check database</h3>";
$verifyQuery = "SELECT * FROM shopping_cart WHERE user_id = ? AND product_id = ?";
$verifyStmt = $conn->prepare($verifyQuery);
if ($verifyStmt) {
    $verifyStmt->bind_param("ii", $user_id, $product_id);
    $verifyStmt->execute();
    $verifyResult = $verifyStmt->get_result();
    
    if ($verifyResult->num_rows > 0) {
        $row = $verifyResult->fetch_assoc();
        echo '<div style="background: #d4edda; padding: 15px; border-radius: 5px; border: 1px solid #28a745; margin: 10px 0;">';
        echo "✅ SUCCESS! Product saved to database!<br>";
        echo "   Product ID: " . $row['product_id'] . "<br>";
        echo "   Quantity: " . $row['quantity'] . "<br>";
        echo "   Added: " . $row['added_at'] . "<br>";
        echo '</div>';
    } else {
        echo '<div style="background: #f8d7da; padding: 15px; border-radius: 5px; border: 1px solid #dc3545; margin: 10px 0;">';
        echo "❌ FAILED! Product NOT found in database after insert!<br>";
        echo "   This means the INSERT statement failed or didn't run.";
        echo '</div>';
    }
    $verifyStmt->close();
} else {
    echo "❌ Verify query failed: " . $conn->error . "<br>";
}

echo "<hr>";
echo "<h3>Summary</h3>";
echo "<p>If all steps above show ✅, then the system is working!</p>";
echo "<p>If any step shows ❌, there's a problem that needs fixing.</p>";
echo "<p><a href='TEST_ADD_TO_CART.php' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>🔄 Run Test Again</a></p>";

$conn->close();
?>
