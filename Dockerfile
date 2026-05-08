# Stage 1: Composer dependencies
FROM composer:2.7 AS vendor

WORKDIR /tmp

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-scripts \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader

# Stage 2: Application image
FROM php:8.3-fpm-alpine

WORKDIR /var/www

# Install PHP extensions
RUN apk add --no-cache \
    libpq-dev \
    libzip-dev \
    oniguruma-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    xml \
    zip

# Install Node.js, npm, and bash
RUN apk add --no-cache nodejs npm bash

# Copy vendor from first stage
COPY --from=vendor /tmp/vendor ./vendor

# Copy application code
COPY . .

# Copy PHP configuration
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

# Setup .env - copy from .env.example if not present in build context
# This ensures image can run even if .env is missing
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Install npm dependencies and build assets
RUN npm ci && npm run build

RUN adduser -D nginx

# Create storage and cache directories
RUN mkdir -p storage/logs bootstrap/cache && \
    chown -R nginx:nginx storage bootstrap/cache

# Fix permissions for entire app directory
RUN chown -R nginx:nginx /var/www

# Copy entrypoint script
COPY docker/app/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
