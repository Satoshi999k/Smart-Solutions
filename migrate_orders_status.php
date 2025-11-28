<?php
// Quick script to update all orders with null/empty status to 'processing'

$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update all orders where status is NULL or empty to 'processing'
$update_query = "UPDATE orders SET status = 'processing' WHERE status IS NULL OR status = ''";

if ($conn->query($update_query) === TRUE) {
    $affected_rows = $conn->affected_rows;
    echo "Success! Updated $affected_rows orders to 'processing' status.";
} else {
    echo "Error updating orders: " . $conn->error;
}

$conn->close();
?>
