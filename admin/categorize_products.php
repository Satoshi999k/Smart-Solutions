<?php
// Script to categorize existing products based on their image paths

$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Category mapping based on image filenames
$category_mapping = array(
    'geforce|rtx|gtx|nvidia|radeon' => 'graphicscard',
    'ideapad|thinkpad|cyborg|aspire|lenovotab|gigabyte_g6x|msicyborg|msithin' => 'laptop',
    'core_i|ryzen' => 'desktop',
    'processor|cpu' => 'processor',
    'memory|ddr|ram' => 'memory',
    'ssd|nvme|m.2' => 'ssd',
    'motherboard|h610|b450' => 'motherboard',
    'power|psu|watt|supply' => 'powersupply',
    'case|casing' => 'pccase',
    'monitor|display' => 'monitor',
    'keyboard' => 'keyboard',
    'mouse' => 'mouse',
    'headset|headphone|audio' => 'headset'
);

// Get all products without categories
$result = $conn->query("SELECT id, name, image FROM products WHERE category IS NULL OR category = ''");

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $search_text = strtolower($row['name'] . ' ' . $row['image']);
        $category = 'other'; // default
        
        foreach ($category_mapping as $pattern => $cat) {
            if (preg_match('/' . $pattern . '/i', $search_text)) {
                $category = $cat;
                break;
            }
        }
        
        $id = $row['id'];
        $safe_category = $conn->real_escape_string($category);
        $conn->query("UPDATE products SET category = '$safe_category' WHERE id = $id");
        echo "✓ Product ID $id set to category: $category<br>";
    }
    echo "<br>✓ All products categorized!<br>";
} else {
    echo "No uncategorized products found.";
}

// Show summary
$result = $conn->query("SELECT category, COUNT(*) as count FROM products GROUP BY category ORDER BY category");
echo "<br><strong>Category Summary:</strong><br>";
while ($row = $result->fetch_assoc()) {
    echo "- " . ucfirst($row['category']) . ": " . $row['count'] . " products<br>";
}

$conn->close();
?>
