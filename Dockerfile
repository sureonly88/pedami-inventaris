# ===== PHP 8.2 FPM =====
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN set -eux; \
    apk add --no-cache \
        bash \
        git \
        unzip \
        curl \
        icu-dev \
        libzip-dev \
        zlib-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        oniguruma-dev \
        libxml2-dev \
        nodejs \
        npm \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        tokenizer \
        xml \
        bcmath \
        zip \
        intl \
        gd \
        opcache

WORKDIR /var/www/html

# Install Composer v2
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy source
COPY . .

# Install PHP dependency
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# Build Vite assets (Filament 3 wajib)
RUN npm install && npm run build

# Permission
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm", "-F"]
