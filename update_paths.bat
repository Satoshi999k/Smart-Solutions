@echo off
echo ========================================
echo  Running Path Update Script
echo ========================================
echo.
echo This will update all file paths to the new structure.
echo Make sure you have run reorganize_files.bat first!
echo.
pause
echo.
echo Running PowerShell script...
powershell -ExecutionPolicy Bypass -File update_paths.ps1
echo.
pause
