# Use an official PHP image with necessary extensions
FROM php:8.1-fpm

# Install required extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql

# Enable other extensions (optional)
RUN docker-php-ext-enable pdo_mysql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install dependencies
RUN if [ -f "composer.json" ]; then composer install; fi

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose PHP port
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
