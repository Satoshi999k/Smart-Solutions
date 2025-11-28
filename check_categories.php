<?php
$conn = new mysqli("localhost", "root", "", "smartsolutions");

echo "<h2>Database Product Check</h2>";
echo "<hr>";

// Check headsets
echo "<h3>Products with 'headset' in name (first 5):</h3>";
$result = $conn->query("SELECT id, name, category FROM products WHERE LOWER(name) LIKE '%headset%' ORDER BY id LIMIT 5");
if ($result && $result->num_rows > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Category</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>'{$row['category']}'</td></tr>";
    }
    echo "</table>";
}

echo "<hr>";

// Check category='laptop'
echo "<h3>Products with category='laptop':</h3>";
$result = $conn->query("SELECT id, name, category FROM products WHERE LOWER(category) = 'laptop' ORDER BY id LIMIT 5");
if ($result && $result->num_rows > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Category</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>'{$row['category']}'</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'><strong>NO LAPTOPS FOUND!</strong></p>";
}

echo "<hr>";

// Check category='headset'
echo "<h3>Products with category='headset':</h3>";
$result = $conn->query("SELECT id, name, category FROM products WHERE LOWER(category) = 'headset' ORDER BY id LIMIT 5");
if ($result && $result->num_rows > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Category</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>'{$row['category']}'</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>No headsets found</p>";
}

echo "<hr>";

// Check all unique categories
echo "<h3>All unique categories:</h3>";
$result = $conn->query("SELECT DISTINCT LOWER(category) as cat FROM products ORDER BY cat");
if ($result && $result->num_rows > 0) {
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        $cat = $row['cat'];
        $count_result = $conn->query("SELECT COUNT(*) as cnt FROM products WHERE LOWER(category) = '$cat'");
        $count_row = $count_result->fetch_assoc();
        echo "<li>'{$cat}' ({$count_row['cnt']} products)</li>";
    }
    echo "</ul>";
}

$conn->close();
?>
