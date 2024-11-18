# Use the official PHP image as the base
FROM php:8.2-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    nano \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy the Laravel app into the container
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Grant write permissions to the storage and cache directories
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Copy the Nginx and Supervisor configuration files
COPY nginx.conf /etc/nginx/conf.d/default.conf
COPY supervisord.conf /etc/supervisord.conf

# Expose port 8000 for external access
EXPOSE 8000

# Start both Nginx and PHP-FPM services via Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]