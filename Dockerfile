FROM richarvey/nginx-php-fpm:3.1.6

COPY . /var/www/html

ENV WEBROOT /var/www/html/public
ENV APP_TYPE laravel
ENV SKIP_COMPOSER 0
ENV PHP_ERRORS_STDERR 1

RUN composer install --no-dev --optimize-autoloader

EXPOSE 80

# 1. Asegurar permisos de dueño y de escritura para el servidor web (www-data)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 2. Comando maestro: 
#    - Configura Nginx para apuntar a /public
#    - Limpia cachés de vistas previas para evitar el error de "Permission denied"
#    - Ejecuta migraciones
#    - Inicia el servidor
CMD sh -c "sed -i 's|root /var/www/html|root /var/www/html/public|g' /etc/nginx/sites-available/default.conf && \
    php artisan view:clear && \
    php artisan config:clear && \
    php artisan migrate --force && \
    supervisord -n"