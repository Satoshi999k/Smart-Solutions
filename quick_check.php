<?php
$conn = new mysqli("localhost", "root", "", "smartsolutions");

echo "<h2>DEBUGGING YOUR CART ISSUE</h2>";
echo "<hr>";

// Get the headsets from the database
$headset_ids = [155, 156]; // From your screenshot, these are likely the IDs

echo "<h3>Checking Headset Products:</h3>";
foreach ($headset_ids as $id) {
    $result = $conn->query("SELECT id, name, category FROM products WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "ID $id: {$row['name']} (Category: '{$row['category']}')<br>";
    }
}

echo "<hr>";

echo "<h3>Count by category:</h3>";
$result = $conn->query("SELECT 
    SUM(CASE WHEN LOWER(category) = 'laptop' THEN 1 ELSE 0 END) as laptop_count,
    SUM(CASE WHEN LOWER(category) = 'headset' THEN 1 ELSE 0 END) as headset_count,
    SUM(CASE WHEN category IS NULL OR category = '' THEN 1 ELSE 0 END) as null_count
FROM products");

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "Laptops: {$row['laptop_count']}<br>";
    echo "Headsets: {$row['headset_count']}<br>";
    echo "NULL/Empty category: {$row['null_count']}<br>";
}

echo "<hr>";

// Check total products
$result = $conn->query("SELECT COUNT(*) as cnt FROM products");
$row = $result->fetch_assoc();
echo "Total products: {$row['cnt']}<br>";

$conn->close();
?>
