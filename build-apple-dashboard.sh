#!/bin/bash

# ============================================
# Apple Dashboard Build Script
# ============================================

echo "🍎 Building Apple-Inspired Dashboard..."
echo ""

# Step 1: Install dependencies
echo "📦 Installing dependencies..."
npm install
echo "✅ Dependencies installed"
echo ""

# Step 2: Build assets
echo "🔨 Building assets..."
npm run build
echo "✅ Assets built"
echo ""

# Step 3: Clear Laravel cache
echo "🧹 Clearing Laravel cache..."
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
echo "✅ Cache cleared"
echo ""

# Step 4: Optimize for production
echo "⚡ Optimizing for production..."
php artisan optimize
echo "✅ Optimized"
echo ""

# Step 5: Set permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage bootstrap/cache
echo "✅ Permissions set"
echo ""

echo "✨ Apple Dashboard build complete!"
echo ""
echo "🚀 Access your dashboard at: http://127.0.0.1:8000/admin"
echo ""
echo "📚 Read APPLE_DASHBOARD_GUIDE.md for documentation"
echo ""
