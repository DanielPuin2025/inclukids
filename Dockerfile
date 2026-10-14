FROM php:8.2-apache

RUN a2enmod headers rewrite \
    && docker-php-ext-install pdo_mysql mysqli

COPY . /var/www/html/IncluKids/

RUN printf '%s\n' '<?php' 'header("Location: /IncluKids/");' 'exit;' \
        > /var/www/html/index.php \
    && rm -f /var/www/html/IncluKids/app/config/database.local.php \
        "/var/www/html/IncluKids/inclukids .sql"

WORKDIR /var/www/html/IncluKids