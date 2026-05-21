ARG PHP_VERSION=8.4
FROM php:${PHP_VERSION}-fpm-alpine

COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
        pdo_mysql \
        mbstring \
        gd \
        opcache \
        zip \
        intl \
        exif

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

COPY php.ini /usr/local/etc/php/conf.d/zz-app.ini

WORKDIR /var/www/html
