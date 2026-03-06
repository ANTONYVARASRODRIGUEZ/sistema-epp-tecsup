FROM richarvey/nginx-php-fpm:3.1.6

COPY . /var/www/html

ENV WEBROOT /var/www/html/public
ENV APP_TYPE laravel
ENV SKIP_COMPOSER 0
ENV PHP_ERRORS_STDERR 1

RUN composer install --no-dev --optimize-autoloader

EXPOSE 80

# 1. Dar permisos a las carpetas de Laravel (vital para que no dé error 500)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 2. Comando para ejecutar migraciones y encender el servidor
CMD sh -c "sed -i 's|root /var/www/html|root /var/www/html/public|g' /etc/nginx/sites-available/default.conf && php artisan migrate --force && supervisord -n"