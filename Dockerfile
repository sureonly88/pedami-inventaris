# ===== Stage: base PHP-FPM =====
FROM php:8.2-fpm-alpine

# Extensions untuk Laravel 11 + Filament 3 (TANPA GD)
RUN set -eux; \
    apk add --no-cache \
        bash \
        git \
        unzip \
        curl \
        icu-dev \
        libzip-dev \
        zlib-dev \
        oniguruma-dev \
        libxml2-dev \
        nodejs \
        npm \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        tokenizer \
        xml \
        bcmath \
        zip \
        intl \
        opcache

WORKDIR /var/www/html

# Composer v2
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_MEMORY_LIMIT=-1

# Copy source
COPY . .

# Install PHP deps
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# Build Vite (WAJIB Filament 3)
RUN npm install && npm run build

# Permission
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm", "-F"]
