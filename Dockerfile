FROM php:8.2-fpm

# Install system & build dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    build-essential \
    libicu-dev \
    libzip-dev \
    zlib1g-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    $PHPIZE_DEPS \
    && rm -rf /var/lib/apt/lists/*

# Configure & install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN docker-php-ext-install -j$(nproc) \
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

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy project
COPY . .

# Prevent composer memory error
ENV COMPOSER_MEMORY_LIMIT=-1

# Install dependencies
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# Install Node 18
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get update && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Build Vite assets
RUN npm install
RUN npm run build

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
