<?php
session_start();
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Get all cart items for this user
    $query = "SELECT sc.id, sc.product_id FROM shopping_cart sc 
              LEFT JOIN products p ON sc.product_id = p.id 
              WHERE sc.user_id = ? AND p.id IS NULL";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $deletedCount = 0;
    while ($row = $result->fetch_assoc()) {
        $deleteQuery = "DELETE FROM shopping_cart WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $row['id']);
        $deleteStmt->execute();
        $deleteStmt->close();
        $deletedCount++;
    }
    
    $stmt->close();
    
    echo "Cleaned up $deletedCount invalid cart items.<br>";
    echo "<a href='cart.php'>Go back to cart</a>";
} else {
    echo "Not logged in";
}

$conn->close();
?>
