FROM php:8.2-apache

# Instalar git
RUN apt-get update && apt-get install -y git && rm -rf /var/lib/apt/lists/*

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Clonar el repositorio y estructurar la carpeta
RUN rm -rf /var/www/html/* \
    && git clone https://github.com/DanielPuin2025/inclukids.git /tmp/inclukids \
    && mkdir -p /var/www/html/IncluKids \
    && cp -r /tmp/inclukids/* /var/www/html/IncluKids/ \
    && cp -r /tmp/inclukids/* /var/www/html/ \
    && rm -rf /tmp/inclukids

# INYECTAR LAS CREDENCIALES DIRECTAMENTE PARA EVITAR PROBLEMAS DE CACHÉ
RUN echo '<?php\n\
defined("INCLUKIDS") or exit;\n\
return [\n\
    "host"     => "sustentaciones2026_inclukids-db",\n\
    "dbname"   => "inclukids",\n\
    "user"     => "inclukids_user",\n\
    "password" => "bye7mwnyt1q9h6enh602",\n\
];' > /var/www/html/app/config/database.local.php

# Configurar Apache
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

# Asegurar permisos correctos
# Clonar el repositorio y aplanar la estructura directamente en la raíz de Apache
RUN rm -rf /var/www/html/* \
    && git clone https://github.com/DanielPuin2025/inclukids.git /tmp/inclukids \
    && cp -r /tmp/inclukids/. /var/www/html/ \
    && rm -rf /tmp/inclukids