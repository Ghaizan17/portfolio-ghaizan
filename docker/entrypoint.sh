#!/bin/sh
# Entrypoint script for Laravel production container

set -e

# Install su-exec for user switching
apk add --no-cache su-exec 2>/dev/null || true

# Run command as www user
run_as_www() {
    su-exec www "$@"
}

# Wait for database if using external DB
if [ -n "$DB_HOST" ] && [ "$DB_HOST" != "localhost" ] && [ "$DB_HOST" != "127.0.0.1" ]; then
    echo "Waiting for database at $DB_HOST:$DB_PORT..."
    while ! nc -z "$DB_HOST" "$DB_PORT" 2>/dev/null; do
        sleep 1
    done
    echo "Database is ready!"
fi

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating APP_KEY..."
    run_as_www php artisan key:generate --force
fi

# Run migrations
echo "Running database migrations..."
run_as_www php artisan migrate --force --no-interaction

# Clear and cache config
echo "Optimizing Laravel..."
run_as_www php artisan config:clear
run_as_www php artisan route:clear
run_as_www php artisan view:clear
run_as_www php artisan config:cache
run_as_www php artisan route:cache
run_as_www php artisan view:cache
run_as_www php artisan event:cache

# Ensure supervisor log directory exists
mkdir -p /var/log/supervisor /var/log/php-fpm
chown -R www:www /var/log/supervisor /var/log/php-fpm

# Start supervisor (runs as root to manage processes)
exec "$@"