FROM php:8.3-fpm

# System dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    zip unzip git curl \
    libzip-dev libpng-dev libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip bcmath opcache

# Timezone fix
RUN echo "date.timezone=UTC" > /usr/local/etc/php/conf.d/timezone.ini

WORKDIR /var/www/html

COPY . .

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# App configuration
RUN php artisan storage:link || true
RUN php artisan config:clear
RUN php artisan config:cache
RUN php artisan route:cache

RUN chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Runtime configs
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8080
CMD ["/usr/bin/supervisord"]
