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
        
        // Load cart items from database with product details
        $query = "SELECT sc.product_id as id, p.name, p.price, p.image, sc.quantity 
                  FROM shopping_cart sc
                  LEFT JOIN products p ON sc.product_id = p.id
                  WHERE sc.user_id = ?";
        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Replace session cart with database cart (database is source of truth)
            $_SESSION['cart'] = [];
            
            while ($row = $result->fetch_assoc()) {
                // Only add items with valid product data (not null)
                if (!empty($row['name']) && !empty($row['price'])) {
                    $_SESSION['cart'][] = $row;
                }
            }
            
            $stmt->close();
        }
        
        $conn->close();
    }
}
?>
