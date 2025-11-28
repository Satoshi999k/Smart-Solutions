<?php
/**
 * Comprehensive Stock System Setup
 * Run this to ensure stock column exists and is properly initialized
 */

$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Stock System Setup & Verification</h2>";

// 1. Check if stock column exists
echo "<h3>Step 1: Checking stock column...</h3>";
$check = $conn->query("SHOW COLUMNS FROM products LIKE 'stock'");

if ($check->num_rows > 0) {
    echo "<p style='color: green;'>✓ Stock column exists</p>";
} else {
    echo "<p style='color: orange;'>⚠ Stock column NOT found. Creating it...</p>";
    
    // Add the stock column
    if ($conn->query("ALTER TABLE products ADD COLUMN stock INT DEFAULT 10 NOT NULL")) {
        echo "<p style='color: green;'>✓ Stock column created successfully</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create stock column: " . $conn->error . "</p>";
        die();
    }
}

// 2. Initialize all products with stock value if not set
echo "<h3>Step 2: Initializing stock values...</h3>";
$check_null = $conn->query("SELECT COUNT(*) as count FROM products WHERE stock IS NULL OR stock = 0");
$result = $check_null->fetch_assoc();
$null_count = $result['count'];

if ($null_count > 0) {
    echo "<p style='color: orange;'>Found " . $null_count . " products with NULL or 0 stock. Initializing to 10...</p>";
    
    if ($conn->query("UPDATE products SET stock = 10 WHERE stock IS NULL OR stock = 0")) {
        echo "<p style='color: green;'>✓ Stock initialized for all products</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to initialize stock: " . $conn->error . "</p>";
    }
} else {
    echo "<p style='color: green;'>✓ All products already have stock values</p>";
}

// 3. Display current stock values
echo "<h3>Step 3: Current Stock Status (First 10 Products)</h3>";
$result = $conn->query("SELECT id, name, stock FROM products ORDER BY id LIMIT 10");

echo "<table border='1' cellpadding='10' style='border-collapse: collapse; margin: 10px 0;'>";
echo "<tr style='background-color: #0062F6; color: white;'>";
echo "<th>Product ID</th>";
echo "<th>Product Name</th>";
echo "<th>Stock</th>";
echo "</tr>";

while ($row = $result->fetch_assoc()) {
    $stock_color = ($row['stock'] > 5) ? 'green' : (($row['stock'] > 0) ? 'orange' : 'red');
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
    echo "<td style='color: $stock_color; font-weight: bold;'>" . $row['stock'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// 4. Test stock update functionality
echo "<h3>Step 4: Testing Stock Update Functionality</h3>";
echo "<p>Testing UPDATE query with prepared statements...</p>";

// Get a product to test with
$test_product = $conn->query("SELECT id, stock FROM products LIMIT 1");
if ($test_product && $test_product->num_rows > 0) {
    $prod = $test_product->fetch_assoc();
    $test_id = $prod['id'];
    $original_stock = $prod['stock'];
    
    echo "<p>Using Product ID: " . $test_id . " (Original Stock: " . $original_stock . ")</p>";
    
    // Test prepared statement (like in process_checkout.php)
    $update_stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
    if ($update_stmt) {
        $test_qty = 1;
        $update_stmt->bind_param("ii", $test_qty, $test_id);
        
        if ($update_stmt->execute()) {
            echo "<p style='color: green;'>✓ Test update executed successfully</p>";
            
            // Verify the update worked
            $verify = $conn->query("SELECT stock FROM products WHERE id = $test_id");
            $verify_row = $verify->fetch_assoc();
            $new_stock = $verify_row['stock'];
            
            echo "<p>Stock after update: " . $new_stock . "</p>";
            
            if ($new_stock == $original_stock - 1) {
                echo "<p style='color: green;'>✓ Stock update verified - decremented correctly!</p>";
            } else {
                echo "<p style='color: red;'>✗ Stock update did not work as expected</p>";
                echo "<p>Expected: " . ($original_stock - 1) . ", Got: " . $new_stock . "</p>";
            }
            
            // Restore the stock value for testing
            $restore_stmt = $conn->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
            $restore_stmt->bind_param("ii", $test_qty, $test_id);
            $restore_stmt->execute();
            $restore_stmt->close();
            
        } else {
            echo "<p style='color: red;'>✗ Test update failed: " . $update_stmt->error . "</p>";
        }
        $update_stmt->close();
    } else {
        echo "<p style='color: red;'>✗ Prepared statement failed: " . $conn->error . "</p>";
    }
}

// 5. Final status
echo "<h3>Step 5: Setup Complete!</h3>";
echo "<p style='color: green;'><strong>✓ Stock system is ready to use</strong></p>";
echo "<p>Stock will be automatically decremented when orders are placed.</p>";

$conn->close();
?>
