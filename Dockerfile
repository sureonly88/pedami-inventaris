FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libicu-dev \
    libzip-dev \
    zlib1g-dev \
    libonig-dev \
    libxml2-dev \
    build-essential \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (tanpa gd dulu)
RUN docker-php-ext-install \
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

# Install Node 18
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get update && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

RUN npm install && npm run build

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
