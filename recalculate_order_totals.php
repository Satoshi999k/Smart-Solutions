<?php
// Script to recalculate order totals based on items in order_details JSON

$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM orders");

if ($result->num_rows > 0) {
    $updated_count = 0;
    
    while ($order = $result->fetch_assoc()) {
        $order_details = json_decode($order['order_details'], true);
        $calculated_total = 0;
        
        // Calculate total from items in order_details
        if (!empty($order_details) && isset($order_details['items'])) {
            foreach ($order_details['items'] as $item) {
                $item_subtotal = (isset($item['subtotal']) ? $item['subtotal'] : 0);
                $calculated_total += $item_subtotal;
            }
        }
        
        // Only update if calculated total is greater than 0 and different from current
        if ($calculated_total > 0 && $calculated_total != $order['total_price']) {
            $update_query = "UPDATE orders SET total_price = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("di", $calculated_total, $order['id']);
            
            if ($update_stmt->execute()) {
                $updated_count++;
                echo "Order #" . $order['id'] . " updated: ₱" . number_format($calculated_total, 2) . "<br>";
            }
            $update_stmt->close();
        }
    }
    
    echo "<br><strong>Total orders updated: $updated_count</strong>";
} else {
    echo "No orders found";
}

$conn->close();
?>
