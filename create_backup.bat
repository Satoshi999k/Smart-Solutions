@echo off
echo ========================================
echo  Smart Solutions - Backup Creator
echo ========================================
echo.

set BACKUP_DIR=backup_%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set BACKUP_DIR=%BACKUP_DIR: =0%

echo Creating backup folder: %BACKUP_DIR%
mkdir %BACKUP_DIR% 2>nul

echo Copying all files to backup...
xcopy *.php %BACKUP_DIR%\ /Y /Q 2>nul
xcopy *.html %BACKUP_DIR%\ /Y /Q 2>nul
xcopy *.css %BACKUP_DIR%\ /Y /Q 2>nul
xcopy *.js %BACKUP_DIR%\ /Y /Q 2>nul
xcopy *.md %BACKUP_DIR%\ /Y /Q 2>nul
xcopy image %BACKUP_DIR%\image\ /E /I /Y /Q 2>nul
xcopy uploads %BACKUP_DIR%\uploads\ /E /I /Y /Q 2>nul

echo.
echo ========================================
echo  Backup Complete!
echo ========================================
echo Backup saved to: %BACKUP_DIR%
echo.
pause
