FROM php:8.2-fpm-alpine

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
        $PHPIZE_DEPS \
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

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_MEMORY_LIMIT=-1

COPY . .

RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
RUN npm install && npm run build

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm", "-F"]
