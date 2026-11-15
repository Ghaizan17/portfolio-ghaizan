#!/bin/sh
# Entrypoint for Laravel production container (Apache + mod_php)

set -e

echo "==> Container starting ($(date -u '+%H:%M:%S'))"

run_as_app() {
    runuser -u www-data -- "$@"
}

export PORT="${PORT:-80}"

# Wait for database (Supabase) using PHP - netcat is not guaranteed on Debian
if [ -n "$DB_HOST" ] && [ "$DB_HOST" != "localhost" ] && [ "$DB_HOST" != "127.0.0.1" ]; then
    echo "Waiting for database at $DB_HOST:$DB_PORT..."
    php -r '
        $host = getenv("DB_HOST");
        $port = (int) getenv("DB_PORT");
        for ($i = 0; $i < 60; $i++) {
            $errno = 0;
            $errstr = "";
            if (@fsockopen($host, $port, $errno, $errstr, 2)) {
                fwrite(STDOUT, "Database is ready!\n");
                exit(0);
            }
            sleep(1);
        }
        fwrite(STDERR, "ERROR: database not reachable after 60s\n");
        exit(1);
    '
fi

# Validate APP_KEY comes from platform environment
if [ -z "$APP_KEY" ]; then
    echo "ERROR: APP_KEY environment variable is not set"
    echo "Generate one locally with: php artisan key:generate --show"
    echo "Then set it in Render Environment Variables"
    exit 1
fi

# Migrations gated by env var (default true for first deploy)
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "==> Running database migrations..."
    # timeout is an external binary: it must run *inside* the wrapper, because a
    # shell function (run_as_app) cannot be executed by timeout directly.
    run_as_app timeout 300 php artisan migrate --force --no-interaction
    echo "==> Migrations done, running filament:upgrade..."
    run_as_app timeout 300 php artisan filament:upgrade || echo "WARN: filament:upgrade failed (non-fatal)"
    echo "==> Post-migrate steps done"
fi

# Ensure runtime directories and permissions
mkdir -p \
    storage/framework/{cache,sessions,views,testing} \
    storage/logs \
    storage/app/public \
    bootstrap/cache \
    /var/log/supervisor

chown -R www-data:www-data storage bootstrap/cache /var/log/supervisor
chmod -R 775 storage bootstrap/cache

# Create storage link if missing
if [ ! -L public/storage ]; then
    run_as_app php artisan storage:link
fi

# Fix HOME: supervisor children inherit root's HOME (/root), which is not
# readable by www-data and breaks libpq SSL cert lookup (postgresql.crt)
export HOME=/var/www/html

# Render only captures stdout/stderr, so also stream application logs there.
# Without this a 500 error is written to storage/logs only and is invisible.
case ",${LOG_STACK:-single}," in
    *,stderr,*) ;;
    *) export LOG_STACK="${LOG_STACK:-single},stderr" ;;
esac

# Substitute PORT into Apache configs
envsubst '$PORT' < /etc/apache2/ports.conf.template > /etc/apache2/ports.conf
envsubst '$PORT' < /etc/apache2/sites-available/000-default.conf.template > /etc/apache2/sites-available/000-default.conf

# Verify Apache config before starting
echo "==> Verifying Apache config..."
apachectl configtest
echo "==> Entrypoint complete, starting supervisor"

# Start supervisor (PID 1) managing apache + queue workers + scheduler
exec "$@"
