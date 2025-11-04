#!/bin/bash
set -e

echo "Starting FileShare Application..."

# Wait for PostgreSQL to be ready
echo "Waiting for PostgreSQL to be ready..."
until pg_isready -h ${DB_HOST:-postgres} -p ${DB_PORT:-5432} -U ${DB_USERNAME:-postgres}; do
    echo "PostgreSQL is unavailable - sleeping"
    sleep 2
done

echo "PostgreSQL is ready!"

# Navigate to application directory
cd /var/www/html

# Set permissions
echo "Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Clear and cache configurations
echo "Optimizing application..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Seed admin user if not exists
echo "Checking for admin user..."
php artisan db:seed --class=AdminSeeder --force || echo "Admin user already exists or seeding failed"

# Create storage link
echo "Creating storage link..."
php artisan storage:link || echo "Storage link already exists"

# Cache configurations for production
if [ "$APP_ENV" = "production" ]; then
    echo "Caching configurations for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

echo "FileShare Application is ready!"

# Execute the main command
exec "$@"
