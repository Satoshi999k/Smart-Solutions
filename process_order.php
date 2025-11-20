<?php
session_start();

// Database connection (replace with your credentials)
$conn = new mysqli("localhost", "root", "", "smartsolutions");

// Error handling for database connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Get data from the form (sanitize inputs!)
$country = $_POST['country'] ?? '';
$firstName = $_POST['first_name'] ?? '';
$lastName = $_POST['last_name'] ?? '';
$address = $_POST['address'] ?? '';
$apartment = $_POST['apartment'] ?? '';
$postalCode = $_POST['postal_code'] ?? '';
$city = $_POST['city'] ?? '';
$region = $_POST['region'] ?? '';
$phone = $_POST['phone'] ?? '';

// Get cart data
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Validate and prepare order details
$orderDetails = [];
$totalPrice = 0;
foreach ($cart as $item) {
    // Validate cart item data (add data type checks)
    if (!isset($item['id'], $item['price']) ||
        !is_numeric($item['id']) || !is_numeric($item['price']) ||
        $item['price'] < 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid cart item data.']);
        exit;
    }
    $orderDetails[] = [
        'product_id' => (int)$item['id'],
        'price' => (float)$item['price']
    ];
    $totalPrice += (float)$item['price'];
}

$orderDetailsJson = json_encode($orderDetails);
$customerDetailsJson = json_encode([
    'country' => $country,
    'first_name' => $firstName,
    'last_name' => $lastName,
    'address' => $address,
    'apartment' => $apartment,
    'postal_code' => $postalCode,
    'city' => $city,
    'region' => $region,
    'phone' => $phone,
    'total_price' => $totalPrice
]);

// Use prepared statements to prevent SQL injection
$sql = "INSERT INTO orders (customer_details, order_details, total_price) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => "Prepare failed: (" . $conn->errno . ") " . $conn->error]);
    exit;
}

// Bind types: customer_details (string), order_details (string), total_price (double)
if (!$stmt->bind_param("ssd", $customerDetailsJson, $orderDetailsJson, $totalPrice)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => "Bind failed: (" . $stmt->errno . ") " . $stmt->error]);
    exit;
}

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => "Execute failed: (" . $stmt->errno . ") " . $stmt->error]);
    exit;
}

// Check if order insertion was successful
if ($stmt->affected_rows > 0) {
    // Order processed successfully. Clear the cart.
    unset($_SESSION['cart']);
    echo json_encode(['success' => true, 'message' => 'Order placed successfully!']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Order insertion failed.']);
}

$stmt->close();
$conn->close();
?>