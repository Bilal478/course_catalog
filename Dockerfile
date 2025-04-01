FROM php:8.3-apache

RUN apt-get update && \
    apt-get install -y libzip-dev && \
    docker-php-ext-install pdo pdo_mysql && \
    a2enmod rewrite


WORKDIR /var/www/html

COPY api/ /var/www/html/

RUN chown -R www-data:www-data /var/www/html && \
    find /var/www/html -type d -exec chmod 755 {} \; && \
    find /var/www/html -type f -exec chmod 644 {} \;


EXPOSE 80