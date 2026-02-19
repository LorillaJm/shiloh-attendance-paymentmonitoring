#!/usr/bin/env bash
# Render Post-Deploy Script - Runs automatically after build

set -o errexit

echo "🚀 Starting post-deploy tasks..."

# Run database migrations
echo "📊 Running migrations..."
php artisan migrate --force --no-interaction

# Clear and optimize caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "⚡ Optimizing..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Warm dashboard cache
echo "🔥 Warming dashboard cache..."
php artisan dashboard:warm-cache || echo "⚠️  Dashboard cache warming skipped (command may not exist yet)"

echo "✅ Post-deploy tasks completed!"
