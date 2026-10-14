FROM php:8.2-apache

# Habilitar mod_rewrite de Apache para las URLs limpias
RUN a2enmod rewrite

# Instalar extensiones necesarias para MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copiar los archivos del proyecto al directorio del servidor web
COPY . /var/www/html/

# Dar permisos adecuados
RUN chown -R www-data:www-data /var/www/html