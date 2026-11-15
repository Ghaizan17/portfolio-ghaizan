# Multi-stage Dockerfile for Laravel Portfolio (Apache + mod_php)
# Stage 1: Build frontend assets
FROM node:22-alpine AS frontend-builder

WORKDIR /app

COPY package*.json ./
RUN npm ci --ignore-scripts

COPY resources/ ./resources/
COPY vite.config.js ./
RUN npm run build

# Stage 2: Build Composer dependencies
FROM composer:2 AS composer-builder

RUN apk add --no-cache icu-dev && \
    docker-php-ext-install -j$(nproc) intl

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Stage 3: Production image - Apache with mod_php (no PHP-FPM, no sockets)
FROM php:8.4-apache AS production

ENV DEBIAN_FRONTEND=noninteractive

# System deps: build headers for PHP exts + runtime tools
RUN apt-get update && apt-get install -y --no-install-recommends \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libicu-dev \
        libpq-dev \
        curl \
        ca-certificates \
        gettext-base \
        supervisor \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        opcache \
        intl \
        zip \
        gd \
        bcmath \
        pcntl

# Redis extension (optional at runtime, cheap to include)
RUN pecl install redis && docker-php-ext-enable redis

# Apache modules for Laravel (.htaccess front controller)
RUN a2enmod rewrite

# PHP config
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-custom.ini

# Apache config templates ($PORT substituted at runtime by entrypoint)
COPY docker/apache/ports.conf /etc/apache2/ports.conf.template
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf.template
RUN echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername

WORKDIR /var/www/html

# Application code (www-data = uid 33, the Apache/mod_php runtime user)
COPY --chown=www-data:www-data . .
COPY --from=frontend-builder --chown=www-data:www-data /app/public/build ./public/build
COPY --from=composer-builder --chown=www-data:www-data /app/vendor ./vendor

# Runtime directories + permissions at build time
RUN mkdir -p \
        storage/framework/{cache,sessions,views,testing} \
        storage/logs \
        storage/app/public \
        bootstrap/cache \
        /var/log/supervisor \
    && chown -R www-data:www-data storage bootstrap/cache /var/log/supervisor \
    && chmod -R 775 storage bootstrap/cache \
    && (php artisan storage:link || true)

# Supervisor config + entrypoint
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/laravel.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD sh -c "curl -fsS http://localhost:\${PORT:-80}/up || exit 1"

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
