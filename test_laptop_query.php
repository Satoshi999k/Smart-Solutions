<?php
$conn = new mysqli("localhost", "root", "", "smartsolutions");

echo "<h2>Testing laptop.php Query</h2>";
echo "<hr>";

// Run the exact same query as laptop.php
$result = $conn->query("SELECT id, name, price, image, category FROM products WHERE LOWER(category) = 'laptop' ORDER BY id DESC");

echo "<h3>Query Result (LOWER(category) = 'laptop'):</h3>";
if ($result && $result->num_rows > 0) {
    echo "Found " . $result->num_rows . " products<br>";
    echo "<table border='1'>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['category']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'><strong>NO PRODUCTS FOUND!</strong></p>";
    
    // Try case-sensitive query
    echo "<h3>Trying case-sensitive query (category = 'laptop'):</h3>";
    $result2 = $conn->query("SELECT id, name, category FROM products WHERE category = 'laptop' LIMIT 5");
    if ($result2 && $result2->num_rows > 0) {
        echo "Found " . $result2->num_rows . " products with exact case<br>";
    } else {
        echo "Also no results with exact case<br>";
    }
    
    // Show ALL unique categories
    echo "<h3>All unique categories in database:</h3>";
    $cat_result = $conn->query("SELECT DISTINCT category FROM products");
    if ($cat_result) {
        while ($cat_row = $cat_result->fetch_assoc()) {
            $cat = $cat_row['category'];
            $count_result = $conn->query("SELECT COUNT(*) as cnt FROM products WHERE category = '$cat'");
            $count = $count_result->fetch_assoc()['cnt'];
            echo "Category: <code>'$cat'</code> - Count: <strong>$count</strong><br>";
        }
    }
}

$conn->close();
?>
