<?php
/**
 * init_cart.php - Initialize cart from database when user logs in
 * Include this at the top of every page to ensure cart is loaded from DB
 */

if (!isset($_SESSION)) {
    session_start();
}

// Only proceed if user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Connect to database
    $conn = new mysqli("localhost", "root", "", "smartsolutions");
    
    if (!$conn->connect_error) {
        // Create table if doesn't exist
        $conn->query("CREATE TABLE IF NOT EXISTS `shopping_cart` (
          `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
          `user_id` INT UNSIGNED NOT NULL,
          `product_id` INT UNSIGNED NOT NULL,
          `quantity` INT NOT NULL DEFAULT 1,
          `added_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
          `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          UNIQUE KEY `user_product` (`user_id`, `product_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        
        // Load cart items from database
        $query = "SELECT product_id, quantity FROM shopping_cart WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Initialize or update session cart from database
            if ($result->num_rows > 0) {
                // If there are items in database, load them
                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }
                
                // Note: We load from DB but don't overwrite session completely
                // This is because user might have added items to session that aren't in DB yet
                while ($row = $result->fetch_assoc()) {
                    $product_id = $row['product_id'];
                    $quantity = $row['quantity'];
                    
                    // Check if this product is already in session
                    $found = false;
                    for ($i = 0; $i < count($_SESSION['cart']); $i++) {
                        if ($_SESSION['cart'][$i]['id'] == $product_id) {
                            // Update to DB quantity (DB is source of truth)
                            $_SESSION['cart'][$i]['quantity'] = $quantity;
                            $found = true;
                            break;
                        }
                    }
                    
                    // If not in session but in DB, add it
                    if (!$found) {
                        $_SESSION['cart'][] = [
                            'id' => $product_id,
                            'name' => 'Product ' . $product_id,
                            'price' => 0,
                            'image' => '',
                            'quantity' => $quantity
                        ];
                    }
                }
            }
            
            $stmt->close();
        }
        
        $conn->close();
    }
}
?>

