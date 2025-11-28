<?php
$conn = new mysqli("localhost", "root", "", "smartsolutions");

echo "<h2>Auto-Categorizing Products</h2>";
echo "<hr>";

$patterns = [
    'laptop' => ['ideapad', 'thinkpad', 'cyborg', 'aspire', 'lenovotab', 'msithin', 'msi thin'],
    'desktop' => ['stratus', 'cirrus', 'cumulus', 'cirrostratus'],
    'processor' => ['core i', 'ryzen', 'processor'],
    'memory' => ['ddr4', 'ddr5', 'ddr3', 'memory', 'ram'],
    'ssd' => ['ssd', 'nvme', 'm.2'],
    'motherboard' => ['motherboard', 'msi', 'asrock', 'asus', 'biostar'],
    'powersupply' => ['power', 'psu', 'watt', 'supply'],
    'pccase' => ['case', 'casing'],
    'monitor' => ['monitor', 'display'],
    'keyboard' => ['keyboard'],
    'mouse' => ['mouse'],
    'headset' => ['headset', 'headphone', 'audio'],
    'graphicscard' => ['geforce', 'rtx', 'gtx', 'nvidia', 'radeon', 'rx', 'videocard']
];

$result = $conn->query("SELECT id, name, image, category FROM products");
$updated = 0;
$total = 0;

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $total++;
        $product_text = strtolower($row['name'] . ' ' . $row['image']);
        $category = null;
        
        foreach ($patterns as $cat => $cat_keywords) {
            foreach ($cat_keywords as $keyword) {
                if (strpos($product_text, strtolower($keyword)) !== false) {
                    $category = $cat;
                    break 2;
                }
            }
        }
        
        if ($category && $row['category'] !== $category) {
            $safe_cat = $conn->real_escape_string($category);
            if ($conn->query("UPDATE products SET category = '$safe_cat' WHERE id = {$row['id']}")) {
                echo "✓ ID {$row['id']}: <strong>$category</strong><br>";
                $updated++;
            }
        }
    }
}

echo "<hr><p>Checked: <strong>$total</strong> | Updated: <strong>$updated</strong></p>";

$conn->close();
?>

