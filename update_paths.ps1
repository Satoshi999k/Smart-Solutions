# Smart Solutions - Path Update Script
# This script updates all file paths after reorganization

Write-Host "========================================" -ForegroundColor Cyan
Write-Host " Smart Solutions - Path Update Script" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$rootPath = "d:\xampp\htdocs\ITP122"

# Get all PHP and HTML files (excluding backups, assets, includes, and products folders)
$files = Get-ChildItem -Path $rootPath -Include *.php,*.html -Recurse -File | Where-Object { 
    $_.FullName -notmatch "\\backup" -and 
    $_.FullName -notmatch "\\assets\\" -and 
    $_.FullName -notmatch "\\includes\\"
}

$totalFiles = $files.Count
$currentFile = 0
$totalChanges = 0

Write-Host "Found $totalFiles files to update" -ForegroundColor Yellow
Write-Host ""

foreach ($file in $files) {
    $currentFile++
    Write-Host "[$currentFile/$totalFiles] Processing: $($file.Name)" -ForegroundColor Green
    
    try {
        $content = Get-Content -Path $file.FullName -Raw -ErrorAction Stop
        $originalContent = $content
        
        # Update CSS paths
        $content = $content -replace 'href="design\.css"', 'href="assets/css/design.css"'
        $content = $content -replace 'href="style\.css"', 'href="assets/css/style.css"'
        $content = $content -replace 'href="animations\.css"', 'href="assets/css/animations.css"'
        
        # Update JS paths
        $content = $content -replace 'src="script\.js"', 'src="assets/js/script.js"'
        $content = $content -replace 'src="app\.js"', 'src="assets/js/app.js"'
        $content = $content -replace 'src="search\.js"', 'src="assets/js/search.js"'
        $content = $content -replace 'src="search-dynamic\.js"', 'src="assets/js/search-dynamic.js"'
        $content = $content -replace 'src="ajax-cart\.js"', 'src="assets/js/ajax-cart.js"'
        
        # Update image paths - more comprehensive patterns
        $content = $content -replace 'src="image/', 'src="assets/images/'
        $content = $content -replace "src='image/", "src='assets/images/"
        $content = $content -replace 'href="image/', 'href="assets/images/'
        $content = $content -replace "href='image/", "href='assets/images/"
        $content = $content -replace '"image/', '"assets/images/'
        $content = $content -replace "'image/", "'assets/images/"
        
        # Fix any double assets/images paths
        $content = $content -replace 'assets/images/assets/images/', 'assets/images/'
        
        # Update include paths
        # Check if file is in products folder to adjust path
        $isInProductsFolder = $file.DirectoryName -match "\\products$"
        
        if ($isInProductsFolder) {
            # Files in products folder need ../ prefix
            $content = $content -replace "require_once 'conn\.php'", "require_once '../includes/conn.php'"
            $content = $content -replace 'require_once "conn\.php"', 'require_once "../includes/conn.php"'
            $content = $content -replace "require_once 'init_cart\.php'", "require_once '../includes/init_cart.php'"
            $content = $content -replace 'require_once "init_cart\.php"', 'require_once "../includes/init_cart.php"'
            $content = $content -replace "require_once 'init_cart_system\.php'", "require_once '../includes/init_cart_system.php'"
            $content = $content -replace 'require_once "init_cart_system\.php"', 'require_once "../includes/init_cart_system.php"'
            
            # Update asset paths for products folder
            $content = $content -replace 'href="assets/css/', 'href="../assets/css/'
            $content = $content -replace 'src="assets/js/', 'src="../assets/js/'
            $content = $content -replace 'src="assets/images/', 'src="../assets/images/'
            $content = $content -replace 'href="assets/images/', 'href="../assets/images/'
            
            # Fix double ../ if already present
            $content = $content -replace '\.\./\.\./assets/', '../assets/'
        } else {
            # Root level files
            $content = $content -replace "require_once 'conn\.php'", "require_once 'includes/conn.php'"
            $content = $content -replace 'require_once "conn\.php"', 'require_once "includes/conn.php"'
            $content = $content -replace "require_once 'init_cart\.php'", "require_once 'includes/init_cart.php'"
            $content = $content -replace 'require_once "init_cart\.php"', 'require_once "includes/init_cart.php"'
            $content = $content -replace "require_once 'init_cart_system\.php'", "require_once 'includes/init_cart_system.php'"
            $content = $content -replace 'require_once "init_cart_system\.php"', 'require_once "includes/init_cart_system.php"'
        }
        
        # Update links to product pages (from root level files)
        if (-not $isInProductsFolder) {
            $content = $content -replace 'href="processor\.php"', 'href="products/processor.php"'
            $content = $content -replace 'href="motherboard\.php"', 'href="products/motherboard.php"'
            $content = $content -replace 'href="graphicscard\.php"', 'href="products/graphicscard.php"'
            $content = $content -replace 'href="memory\.php"', 'href="products/memory.php"'
            $content = $content -replace 'href="ssd\.php"', 'href="products/ssd.php"'
            $content = $content -replace 'href="powersupply\.php"', 'href="products/powersupply.php"'
            $content = $content -replace 'href="pccase\.php"', 'href="products/pccase.php"'
            $content = $content -replace 'href="monitor\.php"', 'href="products/monitor.php"'
            $content = $content -replace 'href="keyboard\.php"', 'href="products/keyboard.php"'
            $content = $content -replace 'href="mouse\.php"', 'href="products/mouse.php"'
            $content = $content -replace 'href="headset\.php"', 'href="products/headset.php"'
            $content = $content -replace 'href="desktop\.php"', 'href="products/desktop.php"'
            $content = $content -replace 'href="laptop\.php"', 'href="products/laptop.php"'
            $content = $content -replace 'href="smartdeals\.php"', 'href="products/smartdeals.php"'
        }
        
        # Check if content changed
        if ($content -ne $originalContent) {
            Set-Content -Path $file.FullName -Value $content -NoNewline -ErrorAction Stop
            Write-Host "  ✓ Updated successfully" -ForegroundColor Cyan
            $totalChanges++
        } else {
            Write-Host "  - No changes needed" -ForegroundColor Gray
        }
    }
    catch {
        Write-Host "  ✗ Error: $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host " Path Update Complete!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Total files processed: $totalFiles" -ForegroundColor White
Write-Host "Total files updated: $totalChanges" -ForegroundColor Green
Write-Host ""
Write-Host "Please test your website to ensure all paths work correctly." -ForegroundColor Yellow
Write-Host "Test at: http://localhost/ITP122/" -ForegroundColor Cyan
Write-Host ""
Read-Host "Press Enter to exit"
