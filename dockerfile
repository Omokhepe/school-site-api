FROM php:8.3-fpm

# System packages
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip bcmath

# Configure Nginx
RUN mkdir -p /run/nginx
COPY ./docker/nginx.conf /etc/nginx/nginx.conf

# Working directory
WORKDIR /var/www/html

# Copy app source
COPY . .

# Environment permissions
RUN chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Composer installation
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install vendor packages
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Laravel optimization
RUN php artisan key:generate --force \
    && php artisan config:cache \
    && php artisan route:cache

# Supervisor config
COPY ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8080

CMD ["/usr/bin/supervisord"]
