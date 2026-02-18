#!/bin/bash

echo "🚀 Deploying Shiloh System Update..."

# Pull latest code
echo "📥 Pulling latest code..."
git pull origin main

# Install dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader

# Run migrations
echo "🗄️  Running migrations..."
php artisan migrate --force

# Clear caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Optimize
echo "⚡ Optimizing..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Seed session types if not exists
echo "📚 Seeding session types..."
php artisan db:seed --class=SessionTypeSeeder --force

# Generate sessions for next 30 days
echo "📅 Generating session occurrences..."
php artisan sessions:generate --days=30

echo "✅ Deployment completed successfully!"
echo ""
echo "⚠️  Remember to:"
echo "  1. Setup cron job for scheduler"
echo "  2. Create student schedules"
echo "  3. Assign teachers to students"
echo "  4. Test parent portal access"
