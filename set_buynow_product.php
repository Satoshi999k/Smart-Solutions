<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Get product ID from POST
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : null;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

error_log("DEBUG: set_buynow_product.php - Received POST: product_id=$product_id");

if ($product_id) {
    // Save the current cart ONLY on first buy-now (when original_cart doesn't exist)
    if (!isset($_SESSION['original_cart'])) {
        $_SESSION['original_cart'] = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $_SESSION['is_buynow_checkout'] = true;
        error_log("DEBUG: set_buynow_product.php - Saved original_cart with " . count($_SESSION['original_cart']) . " items");
    }
    
    // Fetch the product from database to ensure correct data
    $query = "SELECT id, name, price, image FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
        
        // Ensure image path has correct prefix
        $image = $product['image'];
        if (!preg_match('/^(\/|http)/', $image)) {
            $image = '/ITP122/' . $image;
        }
        
        // IMPORTANT: Replace cart with ONLY the buy-now product
        // Clear any existing cart items first
        $_SESSION['cart'] = [
            [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => floatval($product['price']),
                'image' => $image,
                'quantity' => $quantity
            ]
        ];
        error_log("DEBUG: set_buynow_product.php - Cart replaced with product ID $product_id, product name: " . $product['name']);
        
        $stmt->close();
        $conn->close();
        echo json_encode(['success' => true]);
    } else {
        error_log("DEBUG: set_buynow_product.php - Product ID $product_id not found in database");
        $stmt->close();
        $conn->close();
        echo json_encode(['success' => false, 'message' => 'Product not found']);
    }
} else {
    error_log("DEBUG: set_buynow_product.php - Missing product_id");
    $conn->close();
    echo json_encode(['success' => false, 'message' => 'Missing product data']);
}
?>
