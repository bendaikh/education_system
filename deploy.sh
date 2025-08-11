#!/bin/bash

# Laravel Deployment Script for Hostinger
echo "🚀 Starting Laravel deployment..."

# Pull latest changes
echo "📥 Pulling latest changes from Git..."
git pull origin main

# Install/update dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# Install/update npm dependencies and build assets
echo "🎨 Installing NPM dependencies and building assets..."
npm ci
npm run build

# Clear and cache Laravel configurations
echo "⚙️ Optimizing Laravel..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Run database migrations (be careful with this on production)
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Set proper permissions
echo "🔐 Setting file permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# Clear application cache
echo "🧹 Clearing application cache..."
php artisan cache:clear
php artisan view:clear

echo "✅ Deployment completed successfully!"
echo "🌐 Your application should now be live!"
