@echo off
echo ========================================
echo  Smart Solutions - File Reorganization
echo ========================================
echo.

REM Create new folder structure
echo Creating folder structure...
mkdir assets 2>nul
mkdir assets\css 2>nul
mkdir assets\js 2>nul
mkdir assets\images 2>nul
mkdir includes 2>nul
echo Folders created successfully!
echo.

REM Move CSS files
echo Moving CSS files...
move design.css assets\css\ 2>nul
move style.css assets\css\ 2>nul
move animations.css assets\css\ 2>nul
echo CSS files moved!
echo.

REM Move JS files
echo Moving JavaScript files...
move script.js assets\js\ 2>nul
move app.js assets\js\ 2>nul
move search.js assets\js\ 2>nul
move search-dynamic.js assets\js\ 2>nul
move ajax-cart.js assets\js\ 2>nul
echo JavaScript files moved!
echo.

REM Move images folder
echo Moving images...
xcopy image assets\images\ /E /I /Y 2>nul
rmdir /S /Q image 2>nul
echo Images moved!
echo.

REM Move includes
echo Moving include files...
move conn.php includes\ 2>nul
move init_cart.php includes\ 2>nul
move init_cart_system.php includes\ 2>nul
echo Include files moved!
echo.

REM Create and move to products folder
echo Creating products folder...
mkdir products 2>nul
echo Moving product pages...
move processor.php products\ 2>nul
move processor.html products\ 2>nul
move motherboard.php products\ 2>nul
move motherboard.html products\ 2>nul
move graphicscard.php products\ 2>nul
move graphicscard.html products\ 2>nul
move memory.php products\ 2>nul
move memory.html products\ 2>nul
move ssd.php products\ 2>nul
move ssd.html products\ 2>nul
move powersupply.php products\ 2>nul
move powersupply.html products\ 2>nul
move pccase.php products\ 2>nul
move pccase.html products\ 2>nul
move monitor.php products\ 2>nul
move monitor.html products\ 2>nul
move keyboard.php products\ 2>nul
move keyboard.html products\ 2>nul
move mouse.php products\ 2>nul
move mouse.html products\ 2>nul
move headset.php products\ 2>nul
move headset.html products\ 2>nul
move headset1.html products\ 2>nul
move desktop.php products\ 2>nul
move desktop.html products\ 2>nul
move laptop.php products\ 2>nul
move laptop.html products\ 2>nul
move smartdeals.php products\ 2>nul
move smartdeals.html products\ 2>nul
echo Product pages moved!
echo.

echo ========================================
echo  Reorganization Complete!
echo ========================================
echo.
echo Next step: Run update_paths.bat to update all file paths
echo.
pause
