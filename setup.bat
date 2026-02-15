@echo off
echo ========================================
echo Shiloh Attendance System - Setup Script
echo ========================================
echo.

REM Set PATH to include PHP and Composer
set PATH=%PATH%;%USERPROFILE%\bin

echo [1/7] Installing Composer dependencies...
echo This may take 5-10 minutes, please be patient...
php %USERPROFILE%\bin\composer.phar install --no-interaction
if errorlevel 1 (
    echo ERROR: Composer install failed!
    pause
    exit /b 1
)

echo.
echo [2/7] Generating application key...
php artisan key:generate --force
if errorlevel 1 (
    echo ERROR: Key generation failed!
    pause
    exit /b 1
)

echo.
echo [3/7] Installing Node.js dependencies...
where npm >nul 2>nul
if errorlevel 1 (
    echo WARNING: npm not found. Skipping frontend build.
    echo You can install Node.js later from: https://nodejs.org/
    goto skip_npm
)
call npm install
if errorlevel 1 (
    echo WARNING: npm install failed, but continuing...
)

echo.
echo [4/7] Building frontend assets...
call npm run build
if errorlevel 1 (
    echo WARNING: Build failed, but continuing...
)

:skip_npm
echo.
echo [5/7] Running database migrations...
php artisan migrate --force
if errorlevel 1 (
    echo ERROR: Migration failed! Check your database connection in .env
    pause
    exit /b 1
)

echo.
echo [6/7] Seeding admin user...
php artisan db:seed --force
if errorlevel 1 (
    echo ERROR: Seeding failed!
    pause
    exit /b 1
)

echo.
echo [7/7] Optimizing application...
php artisan optimize
if errorlevel 1 (
    echo WARNING: Optimization failed, but continuing...
)

echo.
echo ========================================
echo SETUP SUCCESSFUL!
echo ========================================
echo.
echo You can now start the development server with:
echo   php artisan serve
echo.
echo Then access the admin panel at:
echo   http://localhost:8000/admin
echo.
echo Login credentials:
echo   Email: admin@shiloh.local
echo   Password: Admin123!
echo.
echo ========================================
pause
