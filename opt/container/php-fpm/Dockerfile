FROM rendyananta/php-docker:8.0 AS build-fpm

LABEL org.opencontainers.image.source=https://github.com/rendyananta/sample-todo-app

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY /artisan artisan

# Prevent composer install again if the composer.json file doesn't changed
COPY /composer.json composer.json

RUN composer install --prefer-dist --no-ansi --no-dev --no-autoloader

# Optimize for image building cache
COPY /bootstrap bootstrap
COPY /app app
COPY /config config
COPY /routes routes
COPY . /var/www/html

RUN composer dump-autoload -o

FROM build-fpm AS fpm

COPY --from=build-fpm /var/www/html /var/www/html
