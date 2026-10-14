FROM php:8.2-apache

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Cambiar el DocumentRoot de Apache a una subcarpeta si tus archivos están ahí (ej. public o app), o dejarlo en /var/www/html
# Si tus archivos están sueltos en la raíz del repo, esto asegura permisos y lectura correcta:
RUN echo '<Directory /var/www/html/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/override.conf \
    && a2enconf override

# Instalar extensiones de MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copiar todo el código al directorio de Apache
COPY . /var/www/html/

# Asegurar permisos correctos para Apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html