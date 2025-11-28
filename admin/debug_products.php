<?php
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check what's in the products table
echo "<h2>All Products in Database:</h2>";
$result = $conn->query("SELECT id, name, category, price FROM products ORDER BY category, name LIMIT 50");

$categories = [];
while ($row = $result->fetch_assoc()) {
    if (!isset($categories[$row['category']])) {
        $categories[$row['category']] = [];
    }
    $categories[$row['category']][] = $row;
}

foreach ($categories as $cat => $products) {
    echo "<h3>" . ucfirst($cat) . " (" . count($products) . ")</h3>";
    echo "<ul>";
    foreach ($products as $p) {
        echo "<li>" . $p['id'] . ": " . $p['name'] . " - ₱" . $p['price'] . "</li>";
    }
    echo "</ul>";
}

echo "<br><h2>Test Query Results:</h2>";
echo "<h3>Testing: SELECT id, name, price FROM products WHERE LOWER(category) = 'laptop'</h3>";
$test_result = $conn->query("SELECT id, name, price FROM products WHERE LOWER(category) = 'laptop' ORDER BY id DESC");
echo "Rows found: " . $test_result->num_rows . "<br>";
while ($row = $test_result->fetch_assoc()) {
    echo "- " . $row['id'] . ": " . $row['name'] . " (₱" . $row['price'] . ")<br>";
}

$conn->close();
?>
