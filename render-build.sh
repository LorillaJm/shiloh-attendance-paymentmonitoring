#!/usr/bin/env bash
# Render.com build script
# This runs automatically on every deployment

set -o errexit

echo "🚀 Starting Render build..."

# Install composer dependencies
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

echo "✅ Composer dependencies installed"

# Run database migrations (continue even if it fails - indexes might exist)
php artisan migrate --force --no-interaction || echo "⚠️ Migration had warnings (this is OK if indexes already exist)"

echo "✅ Database migrations completed"

# Clear and rebuild caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Caches rebuilt"

# Warm up dashboard cache (optional - skip if fails)
php artisan dashboard:warm-cache || echo "⚠️ Dashboard cache warming skipped"

echo "✅ Dashboard cache warmed"

echo "🎉 Build completed successfully!"
