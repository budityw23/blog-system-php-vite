FROM php:8.0-fpm

WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files first
COPY composer.json composer.lock* ./

# Set permissions
RUN mkdir -p /app/vendor && \
    chown -R www-data:www-data /app && \
    chmod -R 755 /app

# Install dependencies
RUN composer install --no-scripts --no-autoloader

# Copy the rest of the application
COPY . .

# Generate autoloader
RUN composer dump-autoload --optimize

# Make wait-for-it.sh executable
COPY wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod +x /usr/local/bin/wait-for-it

EXPOSE 8000

# Updated CMD to run both init-db and the PHP server
CMD ["sh", "-c", "wait-for-it db:3306 -- php init-db.php && php -S 0.0.0.0:8000 -t public"]
