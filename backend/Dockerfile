FROM php:8.3-apache

RUN docker-php-ext-install mysqli

RUN a2enmod rewrite

COPY index.php /var/www/html/index.php
COPY db.php /var/www/html/db.php
COPY .htaccess /var/www/html/.htaccess

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
