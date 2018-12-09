FROM php:7.1-apache

RUN docker-php-ext-install pdo_mysql && \
    a2enmod rewrite

ADD . /var/www

EXPOSE 80
