<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in output
ini_set('log_errors', 1); // Log errors to file instead

session_start();
header('Content-Type: application/json; charset=utf-8');

// Database connection
$conn = new mysqli("localhost", "root", "", "smartsolutions");

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Ensure cart exists
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Read POST data
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : null;
$product_name = isset($_POST['product_name']) ? trim($_POST['product_name']) : null;
$product_price = isset($_POST['product_price']) ? floatval($_POST['product_price']) : 0.0;
$product_image = isset($_POST['product_image']) ? trim($_POST['product_image']) : '';
// Quantity requested by user (default 1)
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
$quantity = $quantity < 1 ? 1 : $quantity;

if (!$product_id || !$product_name) {
    echo json_encode(['success' => false, 'message' => 'Missing product information.']);
    $conn->close();
    exit;
}

// Check if item already in cart -> increase quantity by requested amount
$found = false;
for ($i = 0; $i < count($_SESSION['cart']); $i++) {
    if ($_SESSION['cart'][$i]['id'] == $product_id) {
        $_SESSION['cart'][$i]['quantity'] += $quantity;
        $found = true;
        break;
    }
}

// If not found, add new item with quantity 1
if (!$found) {
    $_SESSION['cart'][] = [
        'id' => $product_id,
        'name' => $product_name,
        'price' => $product_price,
        'image' => $product_image,
        'quantity' => $quantity
    ];
}

// If user is logged in, save cart to database
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    try {
        // First, ensure products table exists
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
        
        $conn->query($createProductsTableQuery);
        
        // Create TABLE IF NOT EXISTS - NO foreign key constraint to avoid dependency on products table
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
        
        // Check if product already in user's cart
        $checkQuery = "SELECT id, quantity FROM shopping_cart WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($checkQuery);
        
        if ($stmt) {
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                // Update quantity
                $row = $result->fetch_assoc();
                $newQuantity = $row['quantity'] + $quantity;
                $updateQuery = "UPDATE shopping_cart SET quantity = ?, updated_at = NOW() WHERE user_id = ? AND product_id = ?";
                $updateStmt = $conn->prepare($updateQuery);
                if ($updateStmt) {
                    $updateStmt->bind_param("iii", $newQuantity, $user_id, $product_id);
                    $updateStmt->execute();
                    $updateStmt->close();
                }
            } else {
                // Insert new cart item
                $insertQuery = "INSERT INTO shopping_cart (user_id, product_id, quantity, added_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())";
                $insertStmt = $conn->prepare($insertQuery);
                if ($insertStmt) {
                    $insertStmt->bind_param("iii", $user_id, $product_id, $quantity);
                    $insertStmt->execute();
                    $insertStmt->close();
                }
            }
            
            $stmt->close();
        }
    } catch (Exception $e) {
        // Log the error but don't fail - user can still use session cart
        error_log('Database error in add_to_cart.php: ' . $e->getMessage());
    }
}

$conn->close();

// Return cart summary
// Count number of unique products (not total quantity)
$totalItems = count($_SESSION['cart']);

echo json_encode(['success' => true, 'message' => 'Added to cart', 'total_items' => $totalItems]);
exit;
?>