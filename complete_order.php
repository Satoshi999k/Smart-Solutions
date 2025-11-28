<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create orders table if it doesn't exist
$createOrdersTable = "CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT DEFAULT NULL,
    `customer_details` JSON DEFAULT NULL,
    `order_details` JSON DEFAULT NULL,
    `total_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `status` VARCHAR(50) DEFAULT 'processing',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$conn->query($createOrdersTable);

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $customer_email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : 'guest@smartsolutions.com';
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $address = $conn->real_escape_string($_POST['address']);
    $apartment = $conn->real_escape_string($_POST['apartment']);
    $postal_code = $conn->real_escape_string($_POST['postal_code']);
    $city = $conn->real_escape_string($_POST['city']);
    $region = $conn->real_escape_string($_POST['region']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $country = $conn->real_escape_string($_POST['country']);
    
    // Get cart from session
    $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    
    if (empty($cart)) {
        $_SESSION['error'] = "Your cart is empty!";
        header("Location: cart.php");
        exit();
    }
    
    // Calculate total price and build order items with full details
    $totalPrice = 0;
    
    // Build order items array with full product details from cart
    $orderItems = [];
    foreach ($cart as $item) {
        if (isset($item['id'])) {
            $product_id = $item['id'];
            $product_name = $item['name'] ?? 'Product';
            $product_price = floatval($item['price'] ?? 0);
            $quantity = intval($item['quantity'] ?? 1);
            $subtotal = $product_price * $quantity;
            $totalPrice += $subtotal;
            
            $orderItems[] = [
                'product_id' => $product_id,
                'product_name' => $product_name,
                'quantity' => $quantity,
                'price' => $product_price,
                'subtotal' => $subtotal
            ];
        }
    }
    
    // Prepare order details as JSON with full item information
    $order_details_array = [
        'items' => $orderItems
    ];
    $order_details = json_encode($order_details_array);
    
    // Insert order into database
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
    $payment_method = isset($_POST['payment_method']) ? $conn->real_escape_string($_POST['payment_method']) : 'Not specified';
    
    // Prepare customer details as JSON
    $customer_details = json_encode([
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $customer_email,
        'address' => $address,
        'apartment' => $apartment,
        'city' => $city,
        'region' => $region,
        'postal_code' => $postal_code,
        'country' => $country,
        'phone' => $phone,
        'payment_method' => $payment_method
    ]);
    
    $insertOrderQuery = "INSERT INTO orders (user_id, customer_details, order_details, total_price, status) 
                        VALUES (" . $_SESSION['user_id'] . ", '$customer_details', '$order_details', $totalPrice, 'processing')";
    
    if ($conn->query($insertOrderQuery) === TRUE) {
        $order_id = $conn->insert_id;
        
        // Store order information in session for thank you page
        $_SESSION['order_id'] = $order_id;
        $_SESSION['order_total'] = $totalPrice;
        $_SESSION['customer_name'] = $first_name . ' ' . $last_name;
        $_SESSION['order_items'] = $cart;
        $_SESSION['customer_email'] = $customer_email;
        
        // ============ HANDLE BUY-NOW vs NORMAL CHECKOUT ============
        $is_buynow = (isset($_SESSION['is_buynow_checkout']) && $_SESSION['is_buynow_checkout']) || 
                     (isset($_POST['is_buynow_checkout']) && $_POST['is_buynow_checkout'] == '1');
        
        if ($is_buynow) {
            // For buy-now: DO NOT delete from database, restore original cart
            if (isset($_SESSION['original_cart']) && !empty($_SESSION['original_cart'])) {
                $_SESSION['cart'] = $_SESSION['original_cart'];
            } else {
                unset($_SESSION['cart']);
            }
            unset($_SESSION['original_cart']);
            unset($_SESSION['is_buynow_checkout']);
        } else {
            // For normal checkout: clear the cart from BOTH session and database
            unset($_SESSION['cart']);
            
            // Also delete from database if user is logged in
            if (isset($_SESSION['user_id'])) {
                $user_id = $_SESSION['user_id'];
                $delete_cart = "DELETE FROM shopping_cart WHERE user_id = $user_id";
                $conn->query($delete_cart);
            }
        }
        
        // Set success message
        $_SESSION['success'] = "Order placed successfully! Order ID: " . $order_id;
        
        // Redirect to thank you page
        header("Location: thankyou.php");
        exit();
    } else {
        $_SESSION['error'] = "Error placing order: " . $conn->error;
        header("Location: checkout.php");
        exit();
    }
} else {
    // If not POST request, redirect back to cart
    header("Location: cart.php");
    exit();
}

$conn->close();
?>