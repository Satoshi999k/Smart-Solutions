<?php
/**
 * Quick Path Fixer for Smart Solutions
 * This updates all file paths to new structure
 */

echo "========================================\n";
echo " Smart Solutions - Quick Path Fixer\n";
echo "========================================\n\n";

$rootPath = __DIR__;
$filesUpdated = 0;
$totalFiles = 0;

// Get all PHP and HTML files
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($files as $file) {
    if ($file->isFile() && (preg_match('/\.(php|html)$/', $file->getFilename()))) {
        // Skip files in certain directories
        if (preg_match('/[\\\\\/](assets|includes|backup|products)[\\\\\/]/', $file->getPathname())) {
            continue;
        }
        
        $totalFiles++;
        $filePath = $file->getPathname();
        $content = file_get_contents($filePath);
        $originalContent = $content;
        
        // Update CSS paths
        $content = preg_replace('/href="design\.css"/', 'href="assets/css/design.css"', $content);
        $content = preg_replace('/href="style\.css"/', 'href="assets/css/style.css"', $content);
        $content = preg_replace('/href="animations\.css"/', 'href="assets/css/animations.css"', $content);
        
        // Update JS paths
        $content = preg_replace('/src="script\.js"/', 'src="assets/js/script.js"', $content);
        $content = preg_replace('/src="app\.js"/', 'src="assets/js/app.js"', $content);
        $content = preg_replace('/src="search\.js"/', 'src="assets/js/search.js"', $content);
        $content = preg_replace('/src="search-dynamic\.js"/', 'src="assets/js/search-dynamic.js"', $content);
        $content = preg_replace('/src="ajax-cart\.js"/', 'src="assets/js/ajax-cart.js"', $content);
        
        // Update image paths
        $content = preg_replace('/src="image\//', 'src="assets/images/', $content);
        $content = preg_replace('/src=\'image\//', 'src=\'assets/images/', $content);
        $content = preg_replace('/href="image\//', 'href="assets/images/', $content);
        $content = preg_replace('/href=\'image\//', 'href=\'assets/images/', $content);
        
        // Update include paths
        $content = preg_replace('/require_once [\'"]conn\.php[\'"]/', 'require_once \'includes/conn.php\'', $content);
        $content = preg_replace('/require_once [\'"]init_cart\.php[\'"]/', 'require_once \'includes/init_cart.php\'', $content);
        $content = preg_replace('/require_once [\'"]init_cart_system\.php[\'"]/', 'require_once \'includes/init_cart_system.php\'', $content);
        
        // Update product page links
        $content = preg_replace('/href="processor\.php"/', 'href="products/processor.php"', $content);
        $content = preg_replace('/href="motherboard\.php"/', 'href="products/motherboard.php"', $content);
        $content = preg_replace('/href="graphicscard\.php"/', 'href="products/graphicscard.php"', $content);
        $content = preg_replace('/href="memory\.php"/', 'href="products/memory.php"', $content);
        $content = preg_replace('/href="ssd\.php"/', 'href="products/ssd.php"', $content);
        $content = preg_replace('/href="powersupply\.php"/', 'href="products/powersupply.php"', $content);
        $content = preg_replace('/href="pccase\.php"/', 'href="products/pccase.php"', $content);
        $content = preg_replace('/href="monitor\.php"/', 'href="products/monitor.php"', $content);
        $content = preg_replace('/href="keyboard\.php"/', 'href="products/keyboard.php"', $content);
        $content = preg_replace('/href="mouse\.php"/', 'href="products/mouse.php"', $content);
        $content = preg_replace('/href="headset\.php"/', 'href="products/headset.php"', $content);
        $content = preg_replace('/href="desktop\.php"/', 'href="products/desktop.php"', $content);
        $content = preg_replace('/href="laptop\.php"/', 'href="products/laptop.php"', $content);
        $content = preg_replace('/href="smartdeals\.php"/', 'href="products/smartdeals.php"', $content);
        
        if ($content !== $originalContent) {
            file_put_contents($filePath, $content);
            echo "✓ Updated: " . basename($filePath) . "\n";
            $filesUpdated++;
        }
    }
}

echo "\n========================================\n";
echo " Update Complete!\n";
echo "========================================\n";
echo "Total files scanned: $totalFiles\n";
echo "Files updated: $filesUpdated\n\n";
echo "Please refresh your browser and test the site.\n";
?>
