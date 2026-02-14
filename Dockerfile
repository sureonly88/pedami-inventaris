FROM php:8.2-fpm

WORKDIR /var/www/html

# Install tools saja, tanpa compile
RUN apt-get update && apt-get install -y \
    git unzip curl nodejs npm \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_MEMORY_LIMIT=-1

COPY . .

RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
RUN npm install && npm run build

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
