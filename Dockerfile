FROM php:8.2-apache

# Instalar git y herramientas necesarias
RUN apt-get update && apt-get install -y git && rm -rf /var/lib/apt/lists/*

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Configurar VirtualHost y permitir .htaccess
RUN echo '<VirtualHost *:80>\n\
    ServerAdmin webmaster@localhost\n\
    DocumentRoot /var/www/html\n\
    DirectoryIndex index.php index.html\n\
    <Directory /var/www/html>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Instalar extensiones de MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Limpiar la carpeta por defecto de Apache y clonar tu repositorio directamente
RUN rm -rf /var/www/html/*
RUN git clone https://github.com/DanielPuin2025/inclukids.git /var/www/html/

# Asegurar que Apache sea el dueño de todo y dar permisos abiertos de lectura a carpetas web
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && chmod -R 777 /var/www/html/public