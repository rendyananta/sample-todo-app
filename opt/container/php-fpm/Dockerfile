FROM php:8.0-fpm-alpine AS base

RUN apk add --update zlib-dev freetype-dev libpng-dev jpeg-dev libjpeg-turbo-dev postgresql-dev libpng-dev libxml2-dev libzip-dev bzip2-dev shadow $PHPIZE_DEPS

RUN docker-php-ext-configure \
    opcache --enable-opcache &&\
    docker-php-ext-configure gd --with-jpeg=/usr/include/ --with-freetype=/usr/include/ && \
    docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql &&\
    docker-php-ext-configure zip && \
    docker-php-ext-install \
    opcache \
    pdo_pgsql \
    pgsql \
    pdo \
    sockets \
    intl \
    gd \
    xml \
    bz2 \
    pcntl \
    bcmath \
    exif

COPY /opt/container/php-fpm/opcache.ini $PHP_INI_DIR/conf.d/
COPY /opt/container/php-fpm/php.ini $PHP_INI_DIR/conf.d/

RUN usermod -u 1000 www-data
RUN groupmod -g 1000 www-data
USER www-data

FROM base AS build-fpm

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY /artisan artisan

COPY /composer.json composer.json

RUN composer install --prefer-dist --no-ansi --no-dev --no-autoloader

COPY /bootstrap bootstrap
COPY /app app
COPY /config config
COPY /routes routes
COPY . /var/www/html

RUN composer dump-autoload -o

FROM build-fpm AS fpm

COPY --from=build-fpm /var/www/html /var/www/html
