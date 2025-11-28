<?php
session_start();

// Initialize cart from database (ensures cart has latest data from DB)
// BUT: Skip if in buy-now mode to preserve the selected product
if (!isset($_SESSION['is_buynow_checkout'])) {
    include('../init_cart.php');
} else {
    // We're in buy-now mode, so clear the flag now that we're processing checkout
    unset($_SESSION['is_buynow_checkout']);
}

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

// Get user email from session or database
$user_email = $_SESSION['user_email'] ?? '';

// Always try to fetch from database if we have user_id (most reliable method)
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $email_query = "SELECT email FROM users WHERE id = ?";
    $email_stmt = $conn->prepare($email_query);
    
    if ($email_stmt) {
        $email_stmt->bind_param("i", $user_id);
        $email_stmt->execute();
        $email_result = $email_stmt->get_result();
        if ($email_result && $email_row = $email_result->fetch_assoc()) {
            if (!empty($email_row['email'])) {
                $user_email = $email_row['email'];
            }
        }
        $email_stmt->close();
    }
}

// Fallback if still empty
if (empty($user_email)) {
    $user_email = 'guest@smartsolutions.com';
}

// Get cart from session
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$user_id = $_SESSION['user_id'] ?? 0;

// Handle selected items - can come from POST (from checkout form) or SESSION (from previous page)
$selectedIndices = [];

// DEBUG: Log all POST data
error_log("===== POST DATA DEBUG =====");
error_log("All POST: " . json_encode($_POST));

// First check POST data (from checkout form)
if (isset($_POST['selected_items'])) {
    error_log("Found POST selected_items: " . $_POST['selected_items']);
    $selectedIndices = json_decode($_POST['selected_items'], true);
    error_log("After decode: " . json_encode($selectedIndices));
    if (!is_array($selectedIndices)) {
        $selectedIndices = [];
    }
}

// If no POST data, check SESSION
if (empty($selectedIndices) && isset($_SESSION['selected_cart_indices'])) {
    error_log("Using SESSION selected_cart_indices: " . json_encode($_SESSION['selected_cart_indices']));
    $selectedIndices = $_SESSION['selected_cart_indices'];
}

// Filter cart to only include selected items if they were selected
if (!empty($selectedIndices)) {
    $filteredCart = [];
    foreach ($selectedIndices as $index) {
        if (isset($cart[$index])) {
            $item = $cart[$index];
            // Ensure quantity is set
            if (!isset($item['quantity']) || $item['quantity'] < 1) {
                $item['quantity'] = 1;
            }
            $filteredCart[] = $item;
        }
    }
    $cart = $filteredCart;
    // Clear the selection after checkout
    unset($_SESSION['selected_cart_indices']);
}

error_log("===== CHECKOUT DEBUG START =====");
error_log("Selected indices: " . json_encode($selectedIndices));
error_log("Cart items count: " . count($cart));
foreach ($cart as $idx => $item) {
    error_log("Cart item $idx: ID=" . $item['id'] . ", Qty=" . ($item['quantity'] ?? 'NOT SET'));
}
error_log("===== CHECKOUT DEBUG END =====");

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
    'email' => $user_email,
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
    
    // Update product stock in database using prepared statements
    error_log("======= STOCK UPDATE START =======");
    error_log("Cart count for stock update: " . count($cart));
    
    foreach ($cart as $item) {
        if (isset($item['id'])) {
            $product_id = intval($item['id']);
            $quantity = isset($item['quantity']) ? intval($item['quantity']) : 1;
            
            error_log("Processing stock update: Product ID=$product_id, Quantity=$quantity");
            
            // Decrease stock by the purchased quantity using prepared statement
            $update_stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            if ($update_stmt) {
                $update_stmt->bind_param("ii", $quantity, $product_id);
                $result = $update_stmt->execute();
                if (!$result) {
                    error_log("❌ Stock update FAILED for product $product_id: " . $update_stmt->error);
                } else {
                    error_log("✓ Stock updated: Product $product_id, Quantity decreased by $quantity, Affected rows: " . $update_stmt->affected_rows);
                }
                $update_stmt->close();
            } else {
                error_log("❌ Prepared statement FAILED: " . $conn->error);
            }
        } else {
            error_log("❌ Item missing ID: " . json_encode($item));
        }
    }
    error_log("======= STOCK UPDATE END =======" . PHP_EOL);
    
    // Clear shopping cart from database for purchased items only
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        
        // Remove only the purchased items from database
        foreach ($cart as $item) {
            if (isset($item['id'])) {
                $product_id = intval($item['id']);
                $delete_stmt = $conn->prepare("DELETE FROM shopping_cart WHERE user_id = ? AND product_id = ?");
                if ($delete_stmt) {
                    $delete_stmt->bind_param("ii", $user_id, $product_id);
                    $delete_stmt->execute();
                    $delete_stmt->close();
                }
            }
        }
    }
    
    // Remove purchased items from session cart only
    if (!empty($_SESSION['cart'])) {
        $fullCart = $_SESSION['cart'];
        $selectedIndices = $_SESSION['selected_cart_indices'] ?? [];
        
        if (!empty($selectedIndices)) {
            // Remove items in reverse order to avoid index shifting
            foreach (array_reverse($selectedIndices) as $index) {
                if (isset($fullCart[$index])) {
                    unset($fullCart[$index]);
                }
            }
            // Re-index the array
            $_SESSION['cart'] = array_values($fullCart);
        } else {
            // If no selection data, clear entire cart (Buy Now scenario)
            $_SESSION['cart'] = [];
        }
    }
    
    unset($_SESSION['selected_cart_indices']);
    unset($_SESSION['original_cart']);
    unset($_SESSION['is_buynow_checkout']);
    
    // Set a flag to indicate checkout was successful
    $_SESSION['cart_cleared'] = true;
    
    // Store order info in session for thank you page
    $_SESSION['order_id'] = $order_id;
    $_SESSION['order_total'] = $total_price;
    $_SESSION['customer_name'] = $first_name . ' ' . $last_name;
    
    // Log all stock updates for verification
    error_log("===== ORDER COMPLETED =====");
    error_log("Order ID: $order_id");
    error_log("Items purchased and stock updated:");
    foreach ($cart as $item) {
        error_log("  - Product ID: " . $item['id'] . ", Quantity: " . $item['quantity']);
    }
    error_log("===========================");
    
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
