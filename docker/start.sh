#!/bin/bash
set -e

echo "🚀 Starting Laravel + Filament deployment..."

# Check if APP_KEY is set
if [ -z "$APP_KEY" ]; then
    echo "❌ ERROR: APP_KEY is not set!"
    echo "Generate one with: php artisan key:generate --show"
    exit 1
fi

echo "✅ APP_KEY is set"

# Create supervisor log directory
mkdir -p /var/log/supervisor

# Create directories and set permissions FIRST before any artisan commands
echo "🔒 Creating directories and setting permissions..."
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "📋 Verifying directory structure..."
ls -la /var/www/html/storage/
ls -la /var/www/html/bootstrap/cache/

# Skip database check - let it connect on first request
echo "⚠️  Skipping database check - will connect on first request"

# Run migrations (safe with --force for production)
echo "🗄️  Running database migrations..."
php artisan migrate --force || echo "Migration skipped or failed"

# Publish Livewire and Filament assets
echo "📦 Publishing Livewire and Filament assets..."
php artisan vendor:publish --tag=livewire:assets --force || echo "Livewire assets publish skipped"
php artisan vendor:publish --tag=filament-assets --force || echo "Filament assets publish skipped"

# Verify assets were published
echo "📋 Verifying published assets..."
ls -la /var/www/html/public/vendor/ || echo "No vendor assets directory yet"

# Clear all caches
echo "🔧 Clearing caches..."
php artisan config:clear || echo "Config clear skipped"
php artisan route:clear || echo "Route clear skipped"
php artisan view:clear || echo "View clear skipped"
php artisan cache:clear || echo "Cache clear skipped"

# Only cache config (skip route and view cache to avoid Heroicon issues)
echo "🔧 Caching config only..."
php artisan config:cache || echo "Config cache skipped"

echo "✅ Optimization completed"

# Create storage link if not exists
if [ ! -L /var/www/html/public/storage ]; then
    echo "🔗 Creating storage link..."
    php artisan storage:link || echo "Storage link skipped"
fi

echo "✅ Application ready!"
echo "🌐 Starting web server..."

# Start supervisord
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
