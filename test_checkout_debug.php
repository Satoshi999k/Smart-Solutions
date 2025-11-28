<?php
/**
 * Test script to simulate cart checkout and see what's happening
 */
session_start();

// Set test session
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_email'] = 'test@example.com';
}

// Add a test item to session cart with explicit quantity
$_SESSION['cart'] = [
    [
        'id' => 22,  // AMD Ryzen 7 9700X
        'name' => 'AMD Ryzen 7 9700X 3.8GHz AM5 Socket DDR5 Processor',
        'price' => 25395.00,
        'quantity' => 1
    ]
];

$_SESSION['selected_cart_indices'] = [0];

echo "<h2>Test Debug for Cart Checkout</h2>";
echo "<p><strong>Session User ID:</strong> " . $_SESSION['user_id'] . "</p>";
echo "<p><strong>Session Cart:</strong></p>";
echo "<pre>" . json_encode($_SESSION['cart'], JSON_PRETTY_PRINT) . "</pre>";

// Now simulate the checkout process
echo "<h2>Simulating Checkout Process</h2>";

$conn = new mysqli("localhost", "root", "", "smartsolutions");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. Check current stock
echo "<h3>1. Current Stock</h3>";
$product_id = 22;
$check_query = $conn->prepare("SELECT id, name, stock FROM products WHERE id = ?");
$check_query->bind_param("i", $product_id);
$check_query->execute();
$result = $check_query->get_result();
if ($row = $result->fetch_assoc()) {
    echo "<p>Product: " . htmlspecialchars($row['name']) . "</p>";
    echo "<p><strong>Stock BEFORE:</strong> " . $row['stock'] . "</p>";
    $stock_before = $row['stock'];
} else {
    die("Product not found!");
}
$check_query->close();

// 2. Initialize cart from database
echo "<h3>2. Initialize Cart from Database</h3>";
include __DIR__ . '/../init_cart.php';
echo "<p><strong>Cart after init:</strong></p>";
echo "<pre>" . json_encode($_SESSION['cart'], JSON_PRETTY_PRINT) . "</pre>";

// 3. Get selected indices
$selectedIndices = $_SESSION['selected_cart_indices'] ?? [];
echo "<h3>3. Selected Indices</h3>";
echo "<pre>" . json_encode($selectedIndices) . "</pre>";

// 4. Filter cart
echo "<h3>4. Filter Cart to Selected Items</h3>";
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
if (!empty($selectedIndices)) {
    $filteredCart = [];
    foreach ($selectedIndices as $index) {
        if (isset($cart[$index])) {
            $item = $cart[$index];
            if (!isset($item['quantity']) || $item['quantity'] < 1) {
                $item['quantity'] = 1;
            }
            $filteredCart[] = $item;
        }
    }
    $cart = $filteredCart;
}
echo "<p><strong>Filtered Cart (will be used for checkout):</strong></p>";
echo "<pre>" . json_encode($cart, JSON_PRETTY_PRINT) . "</pre>";

// 5. Execute stock update
echo "<h3>5. Execute Stock Update Query</h3>";
foreach ($cart as $item) {
    if (isset($item['id'])) {
        $product_id = intval($item['id']);
        $quantity = isset($item['quantity']) ? intval($item['quantity']) : 1;
        
        echo "<p>Updating Product ID: " . $product_id . ", Quantity: " . $quantity . "</p>";
        
        $update_stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        if ($update_stmt) {
            $update_stmt->bind_param("ii", $quantity, $product_id);
            $result = $update_stmt->execute();
            if ($result) {
                echo "<p><strong style='color:green'>✓ UPDATE EXECUTED (Affected Rows: " . $update_stmt->affected_rows . ")</strong></p>";
            } else {
                echo "<p><strong style='color:red'>✗ UPDATE FAILED: " . $update_stmt->error . "</strong></p>";
            }
            $update_stmt->close();
        } else {
            echo "<p><strong style='color:red'>✗ Statement prep failed: " . $conn->error . "</strong></p>";
        }
    }
}

// 6. Check stock after update
echo "<h3>6. Stock After Update</h3>";
$check_query = $conn->prepare("SELECT id, name, stock FROM products WHERE id = ?");
$check_query->bind_param("i", $product_id);
$check_query->execute();
$result = $check_query->get_result();
if ($row = $result->fetch_assoc()) {
    echo "<p>Product: " . htmlspecialchars($row['name']) . "</p>";
    echo "<p><strong>Stock AFTER:</strong> " . $row['stock'] . "</p>";
    echo "<p><strong>Difference:</strong> " . ($stock_before - $row['stock']) . " (should be 1)</p>";
} else {
    echo "<p>Product not found after update!</p>";
}
$check_query->close();

// 7. Restore stock for next test
echo "<h3>7. Restoring Stock for Next Test</h3>";
$restore_stmt = $conn->prepare("UPDATE products SET stock = ? WHERE id = ?");
$restore_value = $stock_before;
$restore_stmt->bind_param("ii", $restore_value, $product_id);
$restore_stmt->execute();
echo "<p>✓ Stock restored to: " . $stock_before . "</p>";
$restore_stmt->close();

$conn->close();

?>
<style>
body { font-family: Arial; margin: 20px; }
h2, h3 { color: #0062F6; }
pre { background: #f0f0f0; padding: 10px; overflow-x: auto; }
strong { font-weight: bold; }
</style>
