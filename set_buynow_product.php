<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Get the current cart from SESSION
$current_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
error_log("DEBUG: set_buynow_product.php - SESSION cart items: " . count($current_cart));

// If session cart is empty, fetch from database
if (empty($current_cart) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    // JOIN shopping_cart with products table to get product details
    $cart_query = "SELECT sc.product_id, sc.quantity, p.name, p.price, p.image 
                   FROM shopping_cart sc 
                   LEFT JOIN products p ON sc.product_id = p.id 
                   WHERE sc.user_id = $user_id";
    $result = $conn->query($cart_query);
    error_log("DEBUG: set_buynow_product.php - Fetching from database for user $user_id, rows: " . ($result ? $result->num_rows : 0));
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $current_cart[] = [
                'id' => $row['product_id'],
                'name' => $row['name'] ?? 'Product',
                'price' => $row['price'] ?? 0,
                'image' => $row['image'] ?? '',
                'quantity' => $row['quantity']
            ];
        }
    }
    error_log("DEBUG: set_buynow_product.php - Database cart items: " . count($current_cart));
}

// Save the original cart ONLY if this is the first buy-now call
if (!isset($_SESSION['original_cart'])) {
    $_SESSION['original_cart'] = $current_cart;
    $_SESSION['is_buynow_checkout'] = true;
    error_log("DEBUG: set_buynow_product.php - Saved original_cart with " . count($current_cart) . " items, flag set to true");
} else {
    error_log("DEBUG: set_buynow_product.php - original_cart already exists, not overwriting");
}

// Add only the buy-now product
$product_id = $_POST['product_id'] ?? null;
$product_name = $_POST['product_name'] ?? null;
$product_price = $_POST['product_price'] ?? null;
$product_image = $_POST['product_image'] ?? null;
$quantity = $_POST['quantity'] ?? 1;

if ($product_id && $product_name) {
    // Replace cart with only the buy-now product
    $_SESSION['cart'] = [
        [
            'id' => $product_id,
            'name' => $product_name,
            'price' => $product_price,
            'image' => $product_image,
            'quantity' => $quantity
        ]
    ];
    error_log("DEBUG: set_buynow_product.php - Cart replaced with 1 buy-now product, returning success");
    
    $conn->close();
    echo json_encode(['success' => true]);
} else {
    error_log("DEBUG: set_buynow_product.php - Missing product data, product_id: $product_id, product_name: $product_name");
    $conn->close();
    echo json_encode(['success' => false, 'message' => 'Missing product data']);
}
?>
