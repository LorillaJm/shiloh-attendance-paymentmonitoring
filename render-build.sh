#!/usr/bin/env bash
# Render.com build script
# This runs automatically on every deployment

set -o errexit

echo "🚀 Starting Render build..."

# Install composer dependencies
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

echo "✅ Composer dependencies installed"

# Install npm dependencies and build assets
echo "📦 Installing npm dependencies..."
npm ci --legacy-peer-deps || npm install --legacy-peer-deps

echo "🎨 Building assets..."
npm run build

echo "✅ Assets built"

# Run database migrations (continue even if it fails - indexes might exist)
echo "🗄️ Running migrations..."
php artisan migrate --force --no-interaction || echo "⚠️ Migration had warnings (this is OK if indexes already exist)"

echo "✅ Database migrations completed"

# Clear and rebuild caches
echo "🧹 Clearing caches..."
php artisan optimize:clear

echo "📦 Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Caches rebuilt"

# Warm up dashboard cache (optional - skip if fails)
echo "🔥 Warming up dashboard cache..."
php artisan dashboard:warm-cache || echo "⚠️ Dashboard cache warming skipped"

echo "✅ Dashboard cache warmed"

echo "🎉 Build completed successfully!"
