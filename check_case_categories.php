<?php
$conn = new mysqli("localhost", "root", "", "smartsolutions");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>PC Case Products in Database:</h2>";
$result = $conn->query("SELECT id, name, category FROM products WHERE id >= 83 AND id <= 94 LIMIT 12");
if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Name</th><th>Category</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . ($row['category'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error: " . $conn->error;
}

echo "<h2>All Unique Categories:</h2>";
$result = $conn->query("SELECT DISTINCT LOWER(category) as category FROM products ORDER BY category");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "- " . $row['category'] . "<br>";
    }
}

$conn->close();
?>
