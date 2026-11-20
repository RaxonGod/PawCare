FROM php:8.2-fpm-alpine

# Install system dependencies & PostgreSQL dev libraries
RUN apk add --no-cache \
    postgresql-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql pgsql gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
