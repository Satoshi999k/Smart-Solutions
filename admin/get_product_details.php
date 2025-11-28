<?php
session_start();

// Security check
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit();
    }
}

// Check if product ID is provided
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Product ID not provided']);
    exit();
}

$product_id = intval($_GET['id']);

// Database connection
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Connection failed']);
    exit();
}

// Fetch product data
$result = $conn->query("SELECT * FROM products WHERE id = $product_id");

if ($result->num_rows == 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Product not found']);
    $conn->close();
    exit();
}

$product = $result->fetch_assoc();
$conn->close();

// Return product as JSON
header('Content-Type: application/json');
echo json_encode($product);
?>
