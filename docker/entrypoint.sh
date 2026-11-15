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

# Create .env file from environment variables
echo "Creating .env file..."
cat > /var/www/html/.env << 'EOF'
APP_NAME="${APP_NAME:-Portfolio}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${APP_URL}"

APP_LOCALE="${APP_LOCALE:-en}"
APP_FALLBACK_LOCALE="${APP_FALLBACK_LOCALE:-en}"
APP_FAKER_LOCALE="${APP_FAKER_LOCALE:-en_US}"

APP_MAINTENANCE_DRIVER="${APP_MAINTENANCE_DRIVER:-file}"

BCRYPT_ROUNDS="${BCRYPT_ROUNDS:-12}"

LOG_CHANNEL="${LOG_CHANNEL:-stack}"
LOG_STACK="${LOG_STACK:-single}"
LOG_DEPRECATIONS_CHANNEL="${LOG_DEPRECATIONS_CHANNEL:-null}"
LOG_LEVEL="${LOG_LEVEL:-error}"

DB_CONNECTION="${DB_CONNECTION:-pgsql}"
DB_HOST="${DB_HOST}"
DB_PORT="${DB_PORT:-5432}"
DB_DATABASE="${DB_DATABASE}"
DB_USERNAME="${DB_USERNAME}"
DB_PASSWORD="${DB_PASSWORD}"

SESSION_DRIVER="${SESSION_DRIVER:-database}"
SESSION_LIFETIME="${SESSION_LIFETIME:-120}"
SESSION_ENCRYPT="${SESSION_ENCRYPT:-true}"
SESSION_PATH="${SESSION_PATH:-/}"
SESSION_DOMAIN="${SESSION_DOMAIN}"

BROADCAST_CONNECTION="${BROADCAST_CONNECTION:-log}"
FILESYSTEM_DISK="${FILESYSTEM_DISK:-local}"
QUEUE_CONNECTION="${QUEUE_CONNECTION:-database}"

CACHE_STORE="${CACHE_STORE:-database}"

MAIL_MAILER="${MAIL_MAILER:-smtp}"
MAIL_HOST="${MAIL_HOST}"
MAIL_PORT="${MAIL_PORT:-587}"
MAIL_USERNAME="${MAIL_USERNAME}"
MAIL_PASSWORD="${MAIL_PASSWORD}"
MAIL_ENCRYPTION="${MAIL_ENCRYPTION:-tls}"
MAIL_FROM_ADDRESS="${MAIL_FROM_ADDRESS}"
MAIL_FROM_NAME="${MAIL_FROM_NAME:-Portfolio}"

VITE_APP_NAME="${VITE_APP_NAME:-Portfolio}"

ADMIN_EMAIL="${ADMIN_EMAIL}"
ADMIN_PASSWORD="${ADMIN_PASSWORD}"
SEED_DEMO_CONTENT="${SEED_DEMO_CONTENT:-false}"

UPLOAD_MAX_IMAGE_KB="${UPLOAD_MAX_IMAGE_KB:-4096}"
UPLOAD_MAX_DOCUMENT_KB="${UPLOAD_MAX_DOCUMENT_KB:-10240}"
EOF
chown www:www /var/www/html/.env

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