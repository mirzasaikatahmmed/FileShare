@echo off
echo ============================================
echo   FILE SHARING APPLICATION - QUICK SETUP
echo ============================================
echo.

echo [1/5] Creating storage directories...
mkdir storage\app\private\files 2>nul
mkdir storage\app\public\qrcodes 2>nul
echo Done!
echo.

echo [2/5] Creating storage link...
php artisan storage:link
echo.

echo [3/5] Running migrations...
php artisan migrate
echo.

echo [4/5] Seeding database...
php artisan db:seed --class=AdminSeeder
php artisan db:seed --class=SettingSeeder
echo.

echo [5/5] Clearing caches...
php artisan config:clear
php artisan cache:clear
php artisan view:clear
echo.

echo ============================================
echo   SETUP COMPLETE!
echo ============================================
echo.
echo Admin Login:
echo   Email: admin@fileshare.com
echo   Password: password
echo.
echo To start the application:
echo   1. Run: php artisan serve
echo   2. Visit: http://localhost:8000
echo.
echo ============================================

pause
