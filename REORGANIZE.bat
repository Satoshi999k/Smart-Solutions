@echo off
title Smart Solutions - Complete Reorganization
color 0A

:menu
cls
echo ========================================
echo  SMART SOLUTIONS FILE REORGANIZATION
echo ========================================
echo.
echo  This will reorganize your project into:
echo  - assets/css/     (CSS files)
echo  - assets/js/      (JavaScript files)
echo  - assets/images/  (All images)
echo  - includes/       (PHP includes)
echo.
echo ========================================
echo  IMPORTANT: READ BEFORE CONTINUING
echo ========================================
echo.
echo  1. This will MODIFY your project structure
echo  2. A backup will be created automatically
echo  3. All file paths will be updated
echo  4. Test your site after completion
echo.
echo ========================================
echo  OPTIONS
echo ========================================
echo.
echo  [1] Complete Reorganization (Recommended)
echo  [2] Create Backup Only
echo  [3] View Documentation
echo  [4] Exit
echo.
set /p choice="Enter your choice (1-4): "

if "%choice%"=="1" goto complete
if "%choice%"=="2" goto backup
if "%choice%"=="3" goto docs
if "%choice%"=="4" goto end
goto menu

:complete
cls
echo ========================================
echo  STARTING COMPLETE REORGANIZATION
echo ========================================
echo.

echo [1/4] Creating backup...
call create_backup.bat
if errorlevel 1 (
    echo ERROR: Backup failed!
    pause
    goto menu
)
echo Backup created successfully!
echo.

echo [2/4] Creating folder structure...
mkdir assets 2>nul
mkdir assets\css 2>nul
mkdir assets\js 2>nul
mkdir assets\images 2>nul
mkdir includes 2>nul
echo Folders created!
echo.

echo [3/4] Moving files...
echo   - Moving CSS files...
move design.css assets\css\ >nul 2>&1
move style.css assets\css\ >nul 2>&1
move animations.css assets\css\ >nul 2>&1

echo   - Moving JS files...
move script.js assets\js\ >nul 2>&1
move app.js assets\js\ >nul 2>&1
move search.js assets\js\ >nul 2>&1
move search-dynamic.js assets\js\ >nul 2>&1
move ajax-cart.js assets\js\ >nul 2>&1

echo   - Moving images...
xcopy image assets\images\ /E /I /Y /Q >nul 2>&1
rmdir /S /Q image >nul 2>&1

echo   - Moving includes...
move conn.php includes\ >nul 2>&1
move init_cart.php includes\ >nul 2>&1
move init_cart_system.php includes\ >nul 2>&1

echo   - Creating products folder...
mkdir products >nul 2>&1
echo   - Moving product pages...
move processor.php products\ >nul 2>&1
move processor.html products\ >nul 2>&1
move motherboard.php products\ >nul 2>&1
move motherboard.html products\ >nul 2>&1
move graphicscard.php products\ >nul 2>&1
move graphicscard.html products\ >nul 2>&1
move memory.php products\ >nul 2>&1
move memory.html products\ >nul 2>&1
move ssd.php products\ >nul 2>&1
move ssd.html products\ >nul 2>&1
move powersupply.php products\ >nul 2>&1
move powersupply.html products\ >nul 2>&1
move pccase.php products\ >nul 2>&1
move pccase.html products\ >nul 2>&1
move monitor.php products\ >nul 2>&1
move monitor.html products\ >nul 2>&1
move keyboard.php products\ >nul 2>&1
move keyboard.html products\ >nul 2>&1
move mouse.php products\ >nul 2>&1
move mouse.html products\ >nul 2>&1
move headset.php products\ >nul 2>&1
move headset.html products\ >nul 2>&1
move headset1.html products\ >nul 2>&1
move desktop.php products\ >nul 2>&1
move desktop.html products\ >nul 2>&1
move laptop.php products\ >nul 2>&1
move laptop.html products\ >nul 2>&1
move smartdeals.php products\ >nul 2>&1
move smartdeals.html products\ >nul 2>&1
echo Files moved successfully!
echo.

echo [4/4] Updating file paths...
echo   This may take a moment...
powershell -ExecutionPolicy Bypass -File update_paths.ps1 >nul 2>&1
if errorlevel 1 (
    echo   Warning: Path update script had issues
    echo   You may need to update some paths manually
) else (
    echo   Paths updated successfully!
)
echo.

echo ========================================
echo  REORGANIZATION COMPLETE!
echo ========================================
echo.
echo  Next Steps:
echo  1. Test your website: http://localhost/ITP122/
echo  2. Check all pages load correctly
echo  3. Verify images, CSS, and JS work
echo  4. Test cart and login functionality
echo.
echo  If issues occur:
echo  - Check REORGANIZATION_GUIDE.md
echo  - Restore from backup if needed
echo.
echo  Backup location: Check folder starting with "backup_"
echo.
pause
goto menu

:backup
cls
echo Creating backup...
call create_backup.bat
echo.
pause
goto menu

:docs
cls
echo Opening documentation...
start REORGANIZATION_GUIDE.md
echo.
echo If file didn't open, manually open: REORGANIZATION_GUIDE.md
echo.
pause
goto menu

:end
cls
echo ========================================
echo  Thank you for using Smart Solutions
echo  File Reorganization Tool
echo ========================================
echo.
exit
