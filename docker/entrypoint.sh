#!/bin/sh
# Entrypoint script for Laravel production container

set -e

# Helper to run commands as www user
run_as_www() {
    su-exec www "$@"
}

# Wait for database if using external DB (Supabase)
if [ -n "$DB_HOST" ] && [ "$DB_HOST" != "localhost" ] && [ "$DB_HOST" != "127.0.0.1" ]; then
    echo "Waiting for database at $DB_HOST:$DB_PORT..."
    while ! nc -z "$DB_HOST" "$DB_PORT" 2>/dev/null; do
        sleep 1
    done
    echo "Database is ready!"
fi

# Validate APP_KEY is set
if [ -z "$APP_KEY" ]; then
    echo "ERROR: APP_KEY environment variable is not set"
    echo "Generate one locally with: php artisan key:generate --show"
    echo "Then set it in Render Environment Variables"
    exit 1
fi

# Run migrations if enabled via env var
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "Running database migrations..."
    run_as_www php artisan migrate --force --no-interaction
fi

# Ensure storage and cache directories exist with correct permissions
mkdir -p /var/www/html/storage/framework/{cache,sessions,views,testing}
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/bootstrap/cache
mkdir -p /var/log/supervisor
mkdir -p /var/log/php-fpm
mkdir -p /run/php

chown -R www:www /var/www/html/storage /var/www/html/bootstrap/cache /var/log/supervisor /var/log/php-fpm /run/php
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create storage link if it doesn't exist
if [ ! -L /var/www/html/public/storage ]; then
    run_as_www php artisan storage:link 2>/dev/null || true
fi

# Substitute PORT in nginx config
export PORT="${PORT:-8000}"
envsubst '$PORT' < /etc/nginx/conf.d/default.conf > /etc/nginx/conf.d/default.conf.tmp && mv /etc/nginx/conf.d/default.conf.tmp /etc/nginx/conf.d/default.conf

# Verify nginx config
nginx -t
# Verify PHP-FPM config
php-fpm -t

# Start supervisor (runs as root to manage processes)
exec "$@"