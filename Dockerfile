# ===== Stage: base PHP-FPM =====
FROM php:8.2-fpm-alpine

# Install extensions untuk Laravel 11 + Filament 3
RUN set -eux; \
    apk add --no-cache bash git unzip icu-dev libzip-dev zlib-dev libpng-dev oniguruma-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring tokenizer xml bcmath zip opcache

# Direktori kerja
WORKDIR /var/www/html

# Composer v2 (wajib Laravel 11)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy source
COPY . /var/www/html

# Hindari error memory composer
ENV COMPOSER_MEMORY_LIMIT=-1

# Install dependency production
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# Build Vite (WAJIB untuk Filament 3)
RUN npm install
RUN npm run build

# Permission Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm", "-F"]
