FROM php:8.2-apache

# Habilitar mod_rewrite de Apache para las URLs del sistema
RUN a2enmod rewrite

# Permitir .htaccess y reescribir la ruta raíz de Apache de forma estricta
RUN echo '<Directory /var/www/html/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/override.conf \
    && a2enconf override

# Instalar extensiones necesarias para conectar con MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar todos los archivos del proyecto directamente al contenedor
COPY . /var/www/html/

# Asegurar que Apache sea el dueño absoluto de todos los archivos y permisos de lectura
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;