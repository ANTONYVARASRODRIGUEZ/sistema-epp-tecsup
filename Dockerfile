FROM richarvey/nginx-php-fpm:3.1.6

COPY . /var/www/html

# 1. Variables de entorno para configurar Nginx automáticamente
ENV WEBROOT /var/www/html/public
ENV APP_TYPE laravel
ENV SKIP_COMPOSER 0
ENV PHP_ERRORS_STDERR 1
ENV CONF_LARAVEL_SITE 1 

# 2. Instalación de dependencias de Composer
RUN composer install --no-dev --optimize-autoloader

# 3. ELIMINAR EL PHPINFO() POR DEFECTO
# Esto quita el archivo que te está bloqueando la vista de tu Login
RUN rm -f /var/www/html/index.php

EXPOSE 80

# 4. Permisos de carpetas necesarios para Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 5. Comando de inicio (incluye limpieza, migración y supervisor)
CMD sh -c "php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan cache:clear && \
    php artisan migrate --force && \
    php artisan db:seed --force && \
    /usr/bin/supervisord -n -c /etc/supervisord.conf"