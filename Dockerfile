# Usa una imagen base con PHP-FPM y Nginx
FROM php:8.2-fpm-alpine

# Instala las extensiones de PHP que necesites (ejemplo: mysqli, pdo_mysql)
RUN docker-php-ext-install pdo_mysql mysqli

# Instala Nginx
RUN apk add --no-cache nginx

# Configura Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Copia tu código fuente al directorio web de Nginx
COPY . /var/www/html

# Establece los permisos correctos para el directorio web
RUN chown -R www-data:www-data /var/www/html

# Expone los puertos que usarán Nginx y PHP-FPM
EXPOSE 80 9000

# Comando para iniciar Nginx y PHP-FPM
CMD ["sh", "-c", "nginx -g 'daemon off;' & php-fpm"]