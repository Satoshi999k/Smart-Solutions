<?php
// Update all category PHP files to fetch from database
// Run this once to convert all category files to database-driven

$categories = array(
    'laptop' => 'LAPTOP',
    'desktop' => 'DESKTOP', 
    'processor' => 'PROCESSOR',
    'memory' => 'MEMORY',
    'ssd' => 'SSD',
    'motherboard' => 'MOTHERBOARD',
    'powersupply' => 'POWER SUPPLY',
    'pccase' => 'PC CASE',
    'monitor' => 'MONITOR',
    'keyboard' => 'KEYBOARD',
    'mouse' => 'MOUSE',
    'headset' => 'HEADSET',
    'graphicscard' => 'GRAPHICS CARD'
);

$base_path = __DIR__;
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

foreach ($categories as $category_slug => $category_name) {
    $file_path = $base_path . '/' . $category_slug . '.php';
    
    if (!file_exists($file_path)) {
        echo "File not found: $file_path<br>";
        continue;
    }
    
    $content = file_get_contents($file_path);
    
    // Replace the products array assignment with database fetch
    $db_fetch_code = <<<'PHP'
// Fetch products from database
$db_result = $conn->query("SELECT id, name, price, image FROM products WHERE LOWER(category) = '$CATEGORY_SLUG' ORDER BY id DESC");
$products = [];

if ($db_result && $db_result->num_rows > 0) {
    while ($row = $db_result->fetch_assoc()) {
        // Ensure image paths have proper prefix
        $image = $row['image'];
        if (!preg_match('/^(\/|http)/', $image)) {
            $image = '/ITP122/' . $image;
        }
        $products[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'image' => $image
        ];
    }
}
PHP;
    
    $db_fetch_code = str_replace('$CATEGORY_SLUG', "'$category_slug'", $db_fetch_code);
    
    // Pattern to find the $products array assignment
    $pattern = '/\$products\s*=\s*\[[^\]]*(?:\[[^\]]*\][^\]]*)*\];/s';
    
    if (preg_match($pattern, $content)) {
        // Replace hardcoded array with database fetch
        $content = preg_replace($pattern, $db_fetch_code, $content, 1);
        
        // Also update the foreach to check if products exist
        $old_foreach = '/foreach\s*\(\s*\$products\s+as\s+\$product\s*\)\s*\{/';
        $new_foreach = 'if (!empty($products)) { foreach ($products as $product) {';
        $content = preg_replace($old_foreach, $new_foreach, $content, 1);
        
        // Add closing brace
        $content = str_replace('} // foreach $product', '} } // end if products exist', $content);
        
        if (file_put_contents($file_path, $content)) {
            echo "✓ Updated: $category_slug.php<br>";
        } else {
            echo "✗ Failed to update: $category_slug.php<br>";
        }
    } else {
        echo "⚠ Pattern not found in: $category_slug.php<br>";
    }
}

$conn->close();
echo "<br>Update complete!";
?>
