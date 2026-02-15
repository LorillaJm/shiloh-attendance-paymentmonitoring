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

# Wait for database to be ready
echo "⏳ Waiting for database connection..."
until php artisan db:show 2>/dev/null; do
    echo "Database not ready, waiting..."
    sleep 2
done
echo "✅ Database connected"

# Run migrations
echo "📦 Running database migrations..."
php artisan migrate --force --no-interaction
echo "✅ Migrations completed"

# Clear and optimize caches
echo "🔧 Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan filament:cache-components
echo "✅ Optimization completed"

# Create storage link if not exists
if [ ! -L /var/www/html/public/storage ]; then
    echo "🔗 Creating storage link..."
    php artisan storage:link
fi

# Set final permissions
echo "🔒 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "✅ Application ready!"
echo "🌐 Starting web server..."

# Start supervisord
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
