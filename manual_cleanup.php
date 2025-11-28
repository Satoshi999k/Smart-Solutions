<?php
session_start();

// Show cart before cleanup
echo "Cart before cleanup: " . count($_SESSION['cart']) . " items<br>";
echo "<pre>";
print_r($_SESSION['cart']);
echo "</pre><br>";

// Re-clean it
$conn = new mysqli("localhost", "root", "", "smartsolutions");
if (!$conn->connect_error && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Get cart from database
    $query = "SELECT * FROM shopping_cart WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "Database cart items:<br>";
    while ($row = $result->fetch_assoc()) {
        echo "  Product ID: " . $row['product_id'] . " Qty: " . $row['quantity'] . "<br>";
    }
    $stmt->close();
    $conn->close();
}

echo "<a href='cart.php'>Back to cart</a>";
?>
