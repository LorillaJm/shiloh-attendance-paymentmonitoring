@echo off
echo ========================================
echo  Shiloh Parent Portal Login Setup
echo ========================================
echo.

echo [1/4] Clearing Laravel cache...
php artisan cache:clear
php artisan view:clear
php artisan config:clear
echo Done!
echo.

echo [2/4] Installing Node dependencies...
call npm install
echo Done!
echo.

echo [3/4] Building frontend assets...
call npm run build
echo Done!
echo.

echo [4/4] Starting development server...
echo.
echo ========================================
echo  Setup Complete!
echo ========================================
echo.
echo Parent Portal Login: http://localhost:8000/parent
echo Admin Portal Login: http://localhost:8000/admin
echo.
echo Press Ctrl+C to stop the server
echo ========================================
echo.

php artisan serve
