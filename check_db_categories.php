<?php
$conn = new mysqli("localhost", "root", "", "smartsolutions");

echo "<h2>Database Category Check</h2>";
echo "<hr>";

// Check all categories
$result = $conn->query("SELECT DISTINCT category FROM products ORDER BY category");

if ($result && $result->num_rows > 0) {
    echo "<h3>Products by Category:</h3>";
    while ($row = $result->fetch_assoc()) {
        $cat = $row['category'];
        $count_result = $conn->query("SELECT COUNT(*) as cnt FROM products WHERE category = '$cat'");
        $count = $count_result->fetch_assoc()['cnt'];
        
        // Get sample products
        $sample_result = $conn->query("SELECT id, name FROM products WHERE category = '$cat' LIMIT 3");
        
        echo "<hr>";
        echo "<strong>Category: '$cat'</strong> - Count: <strong>$count</strong><br>";
        
        if ($sample_result && $sample_result->num_rows > 0) {
            echo "<ul>";
            while ($sample = $sample_result->fetch_assoc()) {
                echo "<li>ID {$sample['id']}: {$sample['name']}</li>";
            }
            echo "</ul>";
        }
    }
} else {
    echo "<p>No categories found</p>";
}

echo "<hr>";

// Check products with NULL or empty category
$result = $conn->query("SELECT COUNT(*) as cnt FROM products WHERE category IS NULL OR category = ''");
$row = $result->fetch_assoc();
echo "<strong>Products with NULL/empty category: {$row['cnt']}</strong><br>";

// Show first 5 NULL category products
if ($row['cnt'] > 0) {
    $null_result = $conn->query("SELECT id, name FROM products WHERE category IS NULL OR category = '' LIMIT 5");
    echo "<ul>";
    while ($null_row = $null_result->fetch_assoc()) {
        echo "<li>ID {$null_row['id']}: {$null_row['name']}</li>";
    }
    echo "</ul>";
}

$conn->close();
?>
