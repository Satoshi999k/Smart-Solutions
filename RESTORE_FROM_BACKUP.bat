@echo off
echo Restoring files from backup...

REM Delete the assets folder
if exist "d:\xampp\htdocs\ITP122\assets" rmdir /s /q "d:\xampp\htdocs\ITP122\assets"

REM Delete the products folder
if exist "d:\xampp\htdocs\ITP122\products" rmdir /s /q "d:\xampp\htdocs\ITP122\products"

REM Delete the includes folder
if exist "d:\xampp\htdocs\ITP122\includes" rmdir /s /q "d:\xampp\htdocs\ITP122\includes"

REM Copy PHP files
copy /Y "d:\xampp\htdocs\ITP122\backup_20251114_170938\*.php" "d:\xampp\htdocs\ITP122\"

REM Copy HTML files
copy /Y "d:\xampp\htdocs\ITP122\backup_20251114_170938\*.html" "d:\xampp\htdocs\ITP122\"

REM Copy CSS files
copy /Y "d:\xampp\htdocs\ITP122\backup_20251114_170938\*.css" "d:\xampp\htdocs\ITP122\"

REM Copy JS files
copy /Y "d:\xampp\htdocs\ITP122\backup_20251114_170938\*.js" "d:\xampp\htdocs\ITP122\"

REM Copy MD files
copy /Y "d:\xampp\htdocs\ITP122\backup_20251114_170938\*.md" "d:\xampp\htdocs\ITP122\"

REM Recreate image folder and copy contents
if not exist "d:\xampp\htdocs\ITP122\image" mkdir "d:\xampp\htdocs\ITP122\image"
xcopy "d:\xampp\htdocs\ITP122\backup_20251114_170938\image\*.*" "d:\xampp\htdocs\ITP122\image\" /E /I /Y

REM Recreate uploads folder and copy contents
if not exist "d:\xampp\htdocs\ITP122\uploads" mkdir "d:\xampp\htdocs\ITP122\uploads"
xcopy "d:\xampp\htdocs\ITP122\backup_20251114_170938\uploads\*.*" "d:\xampp\htdocs\ITP122\uploads\" /E /I /Y

echo.
echo Restoration complete! Files have been restored from backup.
echo The assets, products, and includes folders have been removed.
echo.
pause
