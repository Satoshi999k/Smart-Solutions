<?php
header('Content-Type: application/json; charset=utf-8');

$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$product_id) {
    echo json_encode(['error' => 'Product ID required']);
    $conn->close();
    exit;
}

$query = "SELECT stock FROM products WHERE id = ?";
$stmt = $conn->prepare($query);

if ($stmt) {
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode(['stock' => intval($row['stock'])]);
    } else {
        echo json_encode(['stock' => 0]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['error' => 'Database query failed']);
}

$conn->close();
?>
