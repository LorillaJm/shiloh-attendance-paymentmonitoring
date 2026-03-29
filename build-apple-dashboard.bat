@echo off
REM ============================================
REM Apple Dashboard Build Script (Windows)
REM ============================================

echo 🍎 Building Apple-Inspired Dashboard...
echo.

REM Step 1: Install dependencies
echo 📦 Installing dependencies...
call npm install
echo ✅ Dependencies installed
echo.

REM Step 2: Build assets
echo 🔨 Building assets...
call npm run build
echo ✅ Assets built
echo.

REM Step 3: Clear Laravel cache
echo 🧹 Clearing Laravel cache...
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
echo ✅ Cache cleared
echo.

REM Step 4: Optimize for production
echo ⚡ Optimizing for production...
php artisan optimize
echo ✅ Optimized
echo.

echo ✨ Apple Dashboard build complete!
echo.
echo 🚀 Access your dashboard at: http://127.0.0.1:8000/admin
echo.
echo 📚 Read APPLE_DASHBOARD_GUIDE.md for documentation
echo.

pause
