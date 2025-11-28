<?php
session_start();

$conn = new mysqli("localhost", "root", "", "smartsolutions");

echo "<h2>Debug Cart Status</h2>";
echo "<hr>";

// Show session cart
echo "<h3>SESSION CART:</h3>";
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    echo "<pre>";
    print_r($_SESSION['cart']);
    echo "</pre>";
} else {
    echo "Empty session cart";
}

echo "<hr>";

// Show database shopping_cart
echo "<h3>DATABASE shopping_cart TABLE:</h3>";
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $result = $conn->query("SELECT sc.id, sc.product_id, sc.quantity, p.name, p.category FROM shopping_cart sc LEFT JOIN products p ON sc.product_id = p.id WHERE sc.user_id = $user_id");
    
    if ($result && $result->num_rows > 0) {
        echo "Found " . $result->num_rows . " items<br>";
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['product_id']}, Name: {$row['name']}, Category: {$row['category']}, Qty: {$row['quantity']}<br>";
        }
    } else {
        echo "No items in database";
    }
} else {
    echo "Not logged in - checking guest session<br>";
    echo "User ID: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : "None");
}

echo "<hr>";

// Show what products these IDs actually are
echo "<h3>Product lookup for cart IDs:</h3>";
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $id = $item['id'];
        $db_result = $conn->query("SELECT id, name, category FROM products WHERE id = $id");
        if ($db_result && $db_result->num_rows > 0) {
            $db_row = $db_result->fetch_assoc();
            echo "Cart Item ID $id → DB: {$db_row['name']} (Category: {$db_row['category']})<br>";
        } else {
            echo "Cart Item ID $id → NOT FOUND IN DATABASE<br>";
        }
    }
}

$conn->close();
?>
