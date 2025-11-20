<?php
session_start();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: checkout.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$country = $conn->real_escape_string($_POST['country']);
$first_name = $conn->real_escape_string($_POST['first_name']);
$last_name = $conn->real_escape_string($_POST['last_name']);
$address = $conn->real_escape_string($_POST['address']);
$apartment = $conn->real_escape_string($_POST['apartment'] ?? '');
$postal_code = $conn->real_escape_string($_POST['postal_code']);
$city = $conn->real_escape_string($_POST['city']);
$region = $conn->real_escape_string($_POST['region']);
$phone = $conn->real_escape_string($_POST['phone']);

// Get cart from session
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$user_id = $_SESSION['user_id'] ?? 0;

if (empty($cart)) {
    $_SESSION['error'] = "Your cart is empty!";
    header("Location: checkout.php");
    $conn->close();
    exit();
}

// Fetch all products from database
$productsById = [];
$product_query = "SELECT id, name, price FROM products";
$result = $conn->query($product_query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productsById[$row['id']] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => floatval($row['price'])
        ];
    }
}

// Calculate total and build order details
$total_price = 0;
$order_items = [];

foreach ($cart as $item) {
    if (isset($item['id']) && isset($productsById[$item['id']])) {
        $product = $productsById[$item['id']];
        $quantity = isset($item['quantity']) ? intval($item['quantity']) : 1;
        $subtotal = $product['price'] * $quantity;
        $total_price += $subtotal;
        
        $order_items[] = [
            'product_id' => $item['id'],
            'product_name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
}

// Prepare customer details as JSON
$customer_details = [
    'first_name' => $first_name,
    'last_name' => $last_name,
    'email' => $_SESSION['user_email'] ?? 'guest@example.com',
    'phone' => $phone,
    'address' => $address,
    'apartment' => $apartment,
    'city' => $city,
    'postal_code' => $postal_code,
    'region' => $region,
    'country' => $country
];

// Prepare order details as JSON
$order_details = [
    'items' => $order_items,
    'order_date' => date('Y-m-d H:i:s')
];

// Insert order into database
$customer_json = $conn->real_escape_string(json_encode($customer_details));
$order_json = $conn->real_escape_string(json_encode($order_details));

// First, check if user_id column exists in orders table
$columns_check = $conn->query("SHOW COLUMNS FROM orders LIKE 'user_id'");
$has_user_id = ($columns_check && $columns_check->num_rows > 0);

// Add user_id column if it doesn't exist
if (!$has_user_id) {
    $alter_query = "ALTER TABLE orders ADD COLUMN user_id INT UNSIGNED DEFAULT 0 AFTER id";
    $conn->query($alter_query);
}

// Insert order with user_id
$insert_query = "INSERT INTO orders (user_id, customer_details, order_details, total_price, status, order_date) 
                 VALUES ($user_id, '$customer_json', '$order_json', $total_price, 'Completed', NOW())";

// If user_id column doesn't exist in table structure, insert without it
if (!$has_user_id) {
    $insert_query = "INSERT INTO orders (customer_details, order_details, total_price, status, order_date) 
                     VALUES ('$customer_json', '$order_json', $total_price, 'Completed', NOW())";
}

if ($conn->query($insert_query) === TRUE) {
    // Get the order ID
    $order_id = $conn->insert_id;
    
    // Update product stock in database
    foreach ($cart as $item) {
        if (isset($item['id'])) {
            $product_id = intval($item['id']);
            $quantity = isset($item['quantity']) ? intval($item['quantity']) : 1;
            
            // Decrease stock by the purchased quantity
            $update_stock = "UPDATE products SET stock = stock - $quantity WHERE id = $product_id";
            $conn->query($update_stock);
        }
    }
    
    // Clear shopping cart from database for this user
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $delete_cart = "DELETE FROM shopping_cart WHERE user_id = $user_id";
        $conn->query($delete_cart);
    }
    
    // Restore the original cart (if this was a buy-now)
    if (isset($_SESSION['original_cart']) && !empty($_SESSION['original_cart'])) {
        $_SESSION['cart'] = $_SESSION['original_cart'];
        unset($_SESSION['original_cart']);
    } else {
        // Clear the cart from session only if no original cart exists
        unset($_SESSION['cart']);
    }
    
    // Also set a flag to indicate cart was cleared
    $_SESSION['cart_cleared'] = true;
    
    // Store order info in session for thank you page
    $_SESSION['order_id'] = $order_id;
    $_SESSION['order_total'] = $total_price;
    $_SESSION['customer_name'] = $first_name . ' ' . $last_name;
    
    // Redirect to thank you page
    header("Location: ../thankyou.php");
    $conn->close();
    exit();
} else {
    $_SESSION['error'] = "Error processing order: " . $conn->error;
    header("Location: checkout.php");
    $conn->close();
    exit();
}
?>
