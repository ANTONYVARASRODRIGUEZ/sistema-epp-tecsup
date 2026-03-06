FROM richarvey/nginx-php-fpm:3.1.6

COPY . /var/www/html

# Estas variables le dicen a la imagen qué hacer con Nginx automáticamente
ENV WEBROOT /var/www/html/public
ENV APP_TYPE laravel
ENV SKIP_COMPOSER 0
ENV PHP_ERRORS_STDERR 1
# Esta línea es CLAVE: activa la configuración de Laravel en Nginx (incluye el try_files)
ENV CONF_LARAVEL_SITE 1 

RUN composer install --no-dev --optimize-autoloader

EXPOSE 80

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# CMD simplificado: quitamos el 'sed' porque las variables ENV de arriba ya configuran Nginx
CMD sh -c "php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan cache:clear && \
    php artisan migrate --force && \
    php artisan db:seed --force && \
    /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf"