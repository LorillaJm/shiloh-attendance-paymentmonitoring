@echo off
echo 🚀 Deploying Shiloh System Update...

echo 📥 Pulling latest code...
git pull origin main

echo 📦 Installing dependencies...
call composer install --no-dev --optimize-autoloader

echo 🗄️  Running migrations...
call php artisan migrate --force

echo 🧹 Clearing caches...
call php artisan config:clear
call php artisan cache:clear
call php artisan view:clear
call php artisan route:clear

echo ⚡ Optimizing...
call php artisan config:cache
call php artisan route:cache
call php artisan view:cache

echo 📚 Seeding session types...
call php artisan db:seed --class=SessionTypeSeeder --force

echo 📅 Generating session occurrences...
call php artisan sessions:generate --days=30

echo ✅ Deployment completed successfully!
echo.
echo ⚠️  Remember to:
echo   1. Setup task scheduler
echo   2. Create student schedules
echo   3. Assign teachers to students
echo   4. Test parent portal access

pause
