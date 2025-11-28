<?php
/**
 * Final Stock System Verification
 * Check that all fixes are in place
 */

session_start();
$conn = new mysqli("localhost", "root", "", "smartsolutions");

?>
<!DOCTYPE html>
<html>
<head>
<title>Stock System Final Verification</title>
<style>
body { font-family: Arial; margin: 20px; background: #f5f5f5; }
.container { max-width: 900px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
h1 { color: #0062F6; }
.section { margin: 20px 0; padding: 15px; background: #f9f9f9; border-left: 4px solid #0062F6; }
.success { color: #4caf50; font-weight: bold; }
.error { color: #f44336; font-weight: bold; }
.warning { color: #ff9800; font-weight: bold; }
table { width: 100%; border-collapse: collapse; margin: 15px 0; }
th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
th { background-color: #0062F6; color: white; }
ul { line-height: 1.8; }
</style>
</head>
<body>
<div class="container">
<h1>Stock Management System - Final Verification</h1>

<div class="section">
<h2>System Status</h2>

<?php

// Check 1: Stock column
echo "<h3>1. Database Stock Column</h3>";
$check_col = $conn->query("SHOW COLUMNS FROM products LIKE 'stock'");
if ($check_col && $check_col->num_rows > 0) {
    echo "<p class='success'>✓ Stock column exists</p>";
} else {
    echo "<p class='error'>✗ Stock column missing</p>";
}

// Check 2: Product stock values
echo "<h3>2. Sample Products</h3>";
$products = $conn->query("SELECT id, name, stock FROM products LIMIT 5");
echo "<table>";
echo "<tr><th>ID</th><th>Name</th><th>Stock</th></tr>";
while ($p = $products->fetch_assoc()) {
    echo "<tr><td>" . $p['id'] . "</td><td>" . htmlspecialchars(substr($p['name'], 0, 40)) . "</td><td><strong>" . $p['stock'] . "</strong></td></tr>";
}
echo "</table>";

// Check 3: Code verification
echo "<h3>3. Code Implementation</h3>";

// Check conn.php
$conn_content = file_get_contents('../../conn.php');
if (strpos($conn_content, 'ALTER TABLE products ADD COLUMN stock') !== false) {
    echo "<p class='success'>✓ conn.php has auto-init stock column code</p>";
} else {
    echo "<p class='warning'>⚠ conn.php may not have auto-init code</p>";
}

// Check process_checkout.php
$checkout_content = file_get_contents('../../pages/process_checkout.php');
if (strpos($checkout_content, 'UPDATE products SET stock = stock -') !== false) {
    echo "<p class='success'>✓ process_checkout.php has stock deduction code</p>";
} else {
    echo "<p class='error'>✗ Stock deduction code missing!</p>";
}

if (strpos($checkout_content, 'selected_cart_indices') !== false) {
    echo "<p class='success'>✓ process_checkout.php handles selected items</p>";
} else {
    echo "<p class='warning'>⚠ May not handle selected items properly</p>";
}

// Check product pages
echo "<h3>4. Product Pages Stock Display</h3>";
$sample_page = file_get_contents('../../products/laptop.php');
if (strpos($sample_page, 'stock') !== false && strpos($sample_page, 'In Stock') !== false) {
    echo "<p class='success'>✓ Product pages display stock</p>";
} else {
    echo "<p class='error'>✗ Product pages may not show stock</p>";
}

?>

</div>

<div class="section">
<h2>How to Test</h2>
<ol>
<li>Go to any product page (Processors, Memory, etc.)</li>
<li>Note the stock displayed on a product (e.g., "In Stock: 10")</li>
<li>Add that product to your cart with quantity 1</li>
<li>Go to your cart and SELECT that item</li>
<li>Click "Proceed to Checkout"</li>
<li>Complete the checkout process</li>
<li>Return to that product page</li>
<li>The stock should now show "In Stock: 9"</li>
</ol>
</div>

<div class="section">
<h2>Important Notes</h2>
<ul>
<li><strong>Buy Now Flow:</strong> Works directly - stock decreases immediately</li>
<li><strong>Cart Checkout Flow:</strong> Now fixed - stock decreases after checkout</li>
<li><strong>Selected Items:</strong> Only selected items in cart are checked out</li>
<li><strong>Remaining Cart:</strong> Unselected items stay in cart for future checkout</li>
</ul>
</div>

<?php
$conn->close();
?>

</div>
</body>
</html>
