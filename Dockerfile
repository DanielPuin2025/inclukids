FROM php:8.2-apache

# Habilitar mod_rewrite de Apache para las URLs del sistema
RUN a2enmod rewrite

# Cambiar la configuración por defecto de Apache para permitir .htaccess y lectura correcta en /var/www/html
RUN echo '<Directory /var/www/html/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/override.conf \
    && a2enconf override

# Forzar al VirtualHost de Apache a usar /var/www/html explícitamente
RUN sed -i 's!/var/www/html!/var/www/html!g' /etc/apache2/sites-available/000-default.conf

# Instalar extensiones necesarias para conectar con MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copiar todos los archivos del proyecto al servidor web
COPY . /var/www/html/

# Dar permisos totales y correctos al usuario de Apache (www-data)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html