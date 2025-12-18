FROM serversideup/php:8.3-fpm-nginx

# optional extensions you need
RUN install-php-extensions \
    pdo pdo_mysql mbstring zip exif pcntl bcmath

# copy app code
COPY . /var/www/html

# set Laravel permissions
RUN chown -R webuser:webgroup /var/www/html/storage \
    && chown -R webuser:webgroup /var/www/html/bootstrap/cache

# install composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Laravel config cache
RUN php artisan key:generate --force \
    && php artisan config:cache \
    && php artisan route:cache

EXPOSE 8080
