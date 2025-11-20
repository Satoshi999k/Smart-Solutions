<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("<h2 style='color: red;'>❌ Database Connection Failed</h2><p>" . $conn->connect_error . "</p>");
}

echo "<h2>🔍 Cart Persistence Debugger</h2>";
echo "<hr>";

// Display current session info
echo "<h3>📊 Current Session Info</h3>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr style='background-color: #f0f0f0;'><th>Property</th><th>Value</th></tr>";

echo "<tr><td><strong>User Logged In</strong></td><td>";
if (isset($_SESSION['user_id'])) {
    echo "✅ YES (User ID: " . htmlspecialchars($_SESSION['user_id']) . ")";
} else {
    echo "❌ NO (Guest user)";
}
echo "</td></tr>";

echo "<tr><td><strong>Session Cart Items</strong></td><td>";
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    echo count($_SESSION['cart']) . " items";
    echo "<br><pre style='font-size: 11px; background: #f5f5f5; padding: 10px; border-radius: 3px;'>";
    foreach ($_SESSION['cart'] as $index => $item) {
        echo "[$index] {$item['id']}: {$item['name']} - Qty: {$item['quantity']}\n";
    }
    echo "</pre>";
} else {
    echo "No session cart or empty";
}
echo "</td></tr>";

echo "</table>";

echo "<hr>";

// If user is logged in, check database
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    echo "<h3>💾 Database Cart Data (for User ID: " . htmlspecialchars($user_id) . ")</h3>";
    
    // Get user info
    $userQuery = "SELECT email, first_name, last_name FROM users WHERE id = ?";
    $userStmt = $conn->prepare($userQuery);
    $userStmt->bind_param("i", $user_id);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    
    if ($userResult->num_rows > 0) {
        $user = $userResult->fetch_assoc();
        echo "<p><strong>User:</strong> " . htmlspecialchars($user['email']) . " (" . htmlspecialchars($user['first_name'] ?? 'N/A') . " " . htmlspecialchars($user['last_name'] ?? 'N/A') . ")</p>";
    }
    $userStmt->close();
    
    // Get cart data from database
    $cartQuery = "SELECT sc.id, sc.product_id, sc.quantity, sc.added_at, sc.updated_at, p.name, p.price
                  FROM shopping_cart sc
                  LEFT JOIN products p ON sc.product_id = p.id
                  WHERE sc.user_id = ?
                  ORDER BY sc.added_at DESC";
    
    $cartStmt = $conn->prepare($cartQuery);
    $cartStmt->bind_param("i", $user_id);
    $cartStmt->execute();
    $cartResult = $cartStmt->get_result();
    
    if ($cartResult->num_rows > 0) {
        echo "<p style='color: green;'>✅ Found " . $cartResult->num_rows . " items in database</p>";
        
        echo "<table border='1' cellpadding='8' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th>Cart ID</th><th>Product ID</th><th>Product Name</th><th>Quantity</th><th>Price</th><th>Total</th><th>Added</th>";
        echo "</tr>";
        
        $totalValue = 0;
        while ($row = $cartResult->fetch_assoc()) {
            $subtotal = floatval($row['price'] ?? 0) * intval($row['quantity']);
            $totalValue += $subtotal;
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['product_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['name'] ?? 'Unknown') . "</td>";
            echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
            echo "<td>₱" . number_format($row['price'] ?? 0, 2) . "</td>";
            echo "<td>₱" . number_format($subtotal, 2) . "</td>";
            echo "<td style='font-size: 11px;'>" . htmlspecialchars($row['added_at']) . "</td>";
            echo "</tr>";
        }
        
        echo "<tr style='background-color: #f0f0f0; font-weight: bold;'>";
        echo "<td colspan='5' style='text-align: right;'>TOTAL:</td>";
        echo "<td>₱" . number_format($totalValue, 2) . "</td>";
        echo "<td></td>";
        echo "</tr>";
        
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ No items in shopping_cart table for this user</p>";
        echo "<p><strong>Possible reasons:</strong></p>";
        echo "<ul>";
        echo "<li>User hasn't added any products while logged in</li>";
        echo "<li>Cart was cleared/items removed</li>";
        echo "<li>shopping_cart table doesn't exist</li>";
        echo "</ul>";
    }
    
    $cartStmt->close();
} else {
    echo "<h3>⚠️ Not Logged In</h3>";
    echo "<p>To test cart persistence, you need to be logged in.</p>";
    echo "<p><a href='http://localhost/ITP122/login.html'>Go to Login</a></p>";
}

echo "<hr>";

// Table existence check
echo "<h3>🗄️ Table Structure Status</h3>";

$tables = ['shopping_cart', 'users', 'products'];
foreach ($tables as $tableName) {
    $tableCheck = $conn->query("SHOW TABLES LIKE '$tableName'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        echo "<p style='color: green;'>✅ `$tableName` table exists</p>";
    } else {
        echo "<p style='color: red;'>❌ `$tableName` table MISSING</p>";
    }
}

echo "<hr>";

echo "<h3>📋 Instructions</h3>";
echo "<ol>";
echo "<li><strong>If logged in and cart is empty:</strong>";
echo "<ul>";
echo "<li>Go to a <a href='http://localhost/ITP122/processor.php'>product page</a></li>";
echo "<li>Click 'Add to Cart' button</li>";
echo "<li>Add at least 2-3 different products</li>";
echo "<li>Then <a href='?refresh=1'>refresh this page</a> to see session cart updated</li>";
echo "</ul>";
echo "</li>";

echo "<li><strong>Once you have products in cart:</strong>";
echo "<ul>";
echo "<li>Click <a href='http://localhost/ITP122/logout.php'>Logout</a></li>";
echo "<li>Then <a href='http://localhost/ITP122/login.html'>Login again</a></li>";
echo "<li>Come back to this page - database cart should still show the items!</li>";
echo "</ul>";
echo "</li>";

echo "<li><strong>Expected Result:</strong>";
echo "<ul>";
echo "<li>✅ Session cart matches database cart</li>";
echo "<li>✅ Both show the same products and quantities</li>";
echo "<li>✅ After logout/login, items persist</li>";
echo "</ul>";
echo "</li>";
echo "</ol>";

echo "<p style='margin-top: 20px;'>";
echo "<a href='http://localhost/ITP122/' style='padding: 10px 20px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;'>← Back to Home</a>";
echo "<a href='?refresh=1' style='padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;'>🔄 Refresh Debug Info</a>";
echo "</p>";

$conn->close();
?>
