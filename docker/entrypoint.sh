
#!/bin/sh

# ============================================================
# Laravel Production Entrypoint
# Apache + PHP + Supervisor
# Railway Deployment
# ============================================================

set -eu

echo "============================================================"
echo " Laravel Production Container"
echo " Starting at: $(date -u '+%Y-%m-%d %H:%M:%S UTC')"
echo "============================================================"

# ------------------------------------------------------------
# 1. Application user execution
# ------------------------------------------------------------

run_as_app() {
    runuser -u www-data -- "$@"
}

# ------------------------------------------------------------
# 2. Environment configuration
# ------------------------------------------------------------

export PORT="${PORT:-3000}"
export DB_CONNECTION="${DB_CONNECTION:-mysql}"

case "$DB_CONNECTION" in
    mysql|mariadb)
        export DB_PORT="${DB_PORT:-3306}"
        ;;
    pgsql)
        export DB_PORT="${DB_PORT:-5432}"
        ;;
    *)
        echo "ERROR: Unsupported DB_CONNECTION: $DB_CONNECTION"
        echo "Supported: mysql, mariadb, pgsql"
        exit 1
        ;;
esac

echo "==> Application configuration"
echo "    PORT: $PORT"
echo "    DB_CONNECTION: $DB_CONNECTION"

# ------------------------------------------------------------
# 3. Validate database configuration
# ------------------------------------------------------------

if [ -z "${DB_URL:-}" ] && {
    [ -z "${DB_HOST:-}" ] ||
    [ -z "${DB_DATABASE:-}" ] ||
    [ -z "${DB_USERNAME:-}" ]
}; then
    echo "ERROR: Missing database configuration."
    echo "Configure DB_URL or DB_HOST, DB_DATABASE, DB_USERNAME."
    exit 1
fi

# ------------------------------------------------------------
# 4. Wait for database connection
# ------------------------------------------------------------

if [ -n "${DB_HOST:-}" ] &&
   [ "$DB_HOST" != "localhost" ] &&
   [ "$DB_HOST" != "127.0.0.1" ]; then

    echo "==> Waiting for database at $DB_HOST:$DB_PORT..."

    php -r '
        $host = getenv("DB_HOST");
        $port = (int) getenv("DB_PORT");

        for ($i = 0; $i < 60; $i++) {
            $errno = 0;
            $errstr = "";

            $connection = @fsockopen(
                $host,
                $port,
                $errno,
                $errstr,
                2
            );

            if ($connection !== false) {
                fclose($connection);

                echo "Database connection is reachable!\n";
                exit(0);
            }

            sleep(1);
        }

        fwrite(
            STDERR,
            "ERROR: Database not reachable after 60 seconds.\n"
        );

        exit(1);
    '
fi

# ------------------------------------------------------------
# 5. Validate Laravel APP_KEY
# ------------------------------------------------------------

if [ -z "${APP_KEY:-}" ]; then
    echo "ERROR: APP_KEY environment variable is not set."
    echo "Generate using: php artisan key:generate --show"
    exit 1
fi

# ------------------------------------------------------------
# 6. Prepare Laravel runtime directories
# ------------------------------------------------------------

echo "==> Preparing Laravel directories..."

mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/testing \
    storage/logs \
    storage/app/public \
    bootstrap/cache \
    /var/log/supervisor

chown -R www-data:www-data storage bootstrap/cache

chmod -R 775 storage bootstrap/cache

# ------------------------------------------------------------
# 7. Run Laravel database migrations
# ------------------------------------------------------------

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then

    echo "==> Running database migrations..."

    run_as_app timeout 300 \
        php artisan migrate --force --no-interaction

    echo "==> Database migrations completed."

    # --------------------------------------------------------
    # 8. Filament upgrade
    # --------------------------------------------------------

    echo "==> Running Filament upgrade..."

    if ! run_as_app timeout 300 \
        php artisan filament:upgrade; then

        echo "WARNING: Filament upgrade failed."
        echo "Continuing application startup..."
    fi

    echo "==> Post-migration tasks completed."

else

    echo "==> Database migrations skipped (RUN_MIGRATIONS=false)."

fi

# ------------------------------------------------------------
# 9. Laravel storage link
# ------------------------------------------------------------

echo "==> Checking Laravel storage link..."

if [ ! -L public/storage ]; then
    run_as_app php artisan storage:link
fi

# ------------------------------------------------------------
# 10. Runtime environment
# ------------------------------------------------------------

# Fix HOME for www-data and PHP libraries.
export HOME=/var/www/html

# Send Laravel logs to stderr.
case ",${LOG_STACK:-single}," in
    *,stderr,*)
        ;;
    *)
        export LOG_STACK="${LOG_STACK:-single},stderr"
        ;;
esac

# ------------------------------------------------------------
# 11. Apache configuration
# ------------------------------------------------------------

echo "==> Configuring Apache port: $PORT"

if [ ! -f /etc/apache2/ports.conf.template ]; then
    echo "ERROR: Apache ports.conf.template not found."
    exit 1
fi

if [ ! -f /etc/apache2/sites-available/000-default.conf.template ]; then
    echo "ERROR: Apache virtual host template not found."
    exit 1
fi

envsubst '$PORT' \
    < /etc/apache2/ports.conf.template \
    > /etc/apache2/ports.conf

envsubst '$PORT' \
    < /etc/apache2/sites-available/000-default.conf.template \
    > /etc/apache2/sites-available/000-default.conf

# ------------------------------------------------------------
# 12. Verify Apache configuration
# ------------------------------------------------------------

echo "==> Verifying Apache configuration..."

apachectl configtest

echo "==> Apache configuration is valid."

# ------------------------------------------------------------
# 13. Start Supervisor
# ------------------------------------------------------------

echo "============================================================"
echo " Laravel container initialization completed."
echo " Starting Supervisor..."
echo "============================================================"

exec "$@"
