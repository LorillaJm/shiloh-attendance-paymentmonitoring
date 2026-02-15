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

# Skip database check - let it connect on first request
echo "⚠️  Skipping database check - will connect on first request"

# Clear and optimize caches (without database)
echo "🔧 Optimizing application..."
php artisan config:cache || echo "Config cache skipped"
php artisan route:cache || echo "Route cache skipped"
php artisan view:cache || echo "View cache skipped"
php artisan event:cache || echo "Event cache skipped"
php artisan filament:cache-components || echo "Filament cache skipped"
echo "✅ Optimization completed"

# Create storage link if not exists
if [ ! -L /var/www/html/public/storage ]; then
    echo "🔗 Creating storage link..."
    php artisan storage:link || echo "Storage link skipped"
fi

# Set final permissions
echo "🔒 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "✅ Application ready!"
echo "🌐 Starting web server..."

# Start supervisord
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
