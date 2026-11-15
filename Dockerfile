# Multi-stage Dockerfile for Laravel Portfolio
# Stage 1: Build frontend assets
FROM node:22-alpine AS frontend-builder

WORKDIR /app

# Install dependencies
COPY package*.json ./
RUN npm ci --ignore-scripts

# Copy source files
COPY resources/ ./resources/
COPY vite.config.js ./

# Build production assets
RUN npm run build

# Stage 2: Build Composer dependencies
FROM composer:2 AS composer-builder

# Install intl extension required by Laravel
RUN apk add --no-cache icu-dev && \
    docker-php-ext-install -j$(nproc) intl

WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies (production only)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Stage 3: Production image
FROM php:8.4-fpm-alpine AS production

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    oniguruma-dev \
    sqlite-dev \
    postgresql-dev \
    icu-dev \
    linux-headers \
    $PHPIZE_DEPS \
    gettext \
    && rm -rf /var/cache/apk/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    pdo_sqlite \
    mbstring \
    zip \
    gd \
    intl \
    opcache \
    bcmath \
    pcntl

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Configure PHP
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-custom.ini
COPY docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf

# Create non-root user
RUN addgroup -g 1000 -S www && \
    adduser -u 1000 -S www -G www

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY --chown=www:www . .

# Copy built assets from frontend-builder
COPY --from=frontend-builder --chown=www:www /app/public/build ./public/build

# Copy vendor from composer-builder
COPY --from=composer-builder --chown=www:www /app/vendor ./vendor

# Create necessary directories and set permissions at build time
RUN mkdir -p \
    storage/framework/{cache,sessions,views,testing} \
    storage/logs \
    storage/app/public \
    bootstrap/cache \
    /var/log/supervisor \
    /var/log/php-fpm \
    /run/php \
    && chown -R www:www storage bootstrap/cache /var/log/supervisor /var/log/php-fpm /run/php \
    && chmod -R 775 storage bootstrap/cache

# Create storage link
RUN php artisan storage:link 2>/dev/null || true

# Copy nginx and supervisor configs
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port (Render uses PORT env var, default 8000)
EXPOSE 8000

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD sh -c "curl -f http://localhost:\${PORT:-8000}/up || exit 1"

# Entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]