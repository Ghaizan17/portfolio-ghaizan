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
        $deadline = microtime(true) + 60;

        do {
            $remaining = $deadline - microtime(true);

            if ($remaining <= 0) {
                break;
            }

            $errno = 0;
            $errstr = "";

            $connection = @fsockopen(
                $host,
                $port,
                $errno,
                $errstr,
                min(2, $remaining)
            );

            if ($connection !== false) {
                fclose($connection);

                echo "Database connection is reachable!\n";
                exit(0);
            }

            usleep((int) (min(1, max(0, $deadline - microtime(true))) * 1000000));
        } while (microtime(true) < $deadline);

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

    echo "==> Running deployment migrations under a database lock..."

    run_as_app php -r '
        require "vendor/autoload.php";
        $app = require "bootstrap/app.php";
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        $database = Illuminate\Support\Facades\DB::connection();
        $driver = $database->getDriverName();

        if (in_array($driver, ["mysql", "mariadb"], true)) {
            $lockName = "portfolio_schema_migrations";
            $lockQuery = "SELECT GET_LOCK(?, 0) AS acquired";
            $lockBindings = [$lockName];
            $unlockQuery = "SELECT RELEASE_LOCK(?)";
            $unlockBindings = [$lockName];
        } elseif ($driver === "pgsql") {
            $lockQuery = "SELECT pg_try_advisory_lock(1879048191, 1) AS acquired";
            $lockBindings = [];
            $unlockQuery = "SELECT pg_advisory_unlock(1879048191, 1)";
            $unlockBindings = [];
        } else {
            fwrite(STDERR, "ERROR: Database migration lock is unsupported for $driver.\n");
            exit(1);
        }

        $deadline = microtime(true) + 120;
        $locked = false;

        do {
            $result = $database->selectOne($lockQuery, $lockBindings);
            $value = (string) ($result->acquired ?? "");

            if (in_array($value, ["1", "t", "true"], true)) {
                $locked = true;
                break;
            }

            usleep(500000);
        } while (microtime(true) < $deadline);

        if (! $locked) {
            fwrite(STDERR, "ERROR: Timed out waiting for the database migration lock.\n");
            exit(1);
        }

        echo "==> Database migration lock acquired.\n";

        $artisan = escapeshellarg(PHP_BINARY)." ".escapeshellarg(base_path("artisan"));
        $exitCode = 0;

        try {
            passthru("timeout 300 $artisan migrate --force --no-interaction", $migrationExitCode);

            if ($migrationExitCode !== 0) {
                $exitCode = $migrationExitCode;
            } else {
                echo "==> Running Filament upgrade...\n";
                passthru("timeout 300 $artisan filament:upgrade --no-interaction", $filamentExitCode);

                if ($filamentExitCode !== 0) {
                    fwrite(STDERR, "WARNING: Filament upgrade failed; continuing application startup.\n");
                }
            }
        } finally {
            $database->selectOne($unlockQuery, $unlockBindings);
            echo "==> Database migration lock released.\n";
        }

        exit($exitCode);
    '

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

for module in mpm_event mpm_worker; do
    if [ -e "/etc/apache2/mods-enabled/${module}.load" ]; then
        echo "==> Disabling incompatible Apache module: $module"
        a2dismod "$module"
    fi
done

a2enmod mpm_prefork

apache_modules="$(apachectl -M 2>&1 || true)"
mpm_module_count="$(printf '%s\n' "$apache_modules" | awk '/mpm_[[:alnum:]_]+_module/ { count++ } END { print count + 0 }')"

echo "==> Apache MPM modules detected: $mpm_module_count"

if [ "$mpm_module_count" -ne 1 ]; then
    printf '%s\n' "$apache_modules" >&2
    ls -l /etc/apache2/mods-enabled/*mpm* >&2 || true
    echo "ERROR: Apache must have exactly one MPM enabled."
    exit 1
fi

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
