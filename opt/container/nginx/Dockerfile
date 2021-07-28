FROM node:15.5-alpine AS assets-build

WORKDIR /var/www/html

COPY . /var/www/html/

RUN npm ci
RUN npm run production

FROM nginx:1.19-alpine AS nginx

RUN adduser -D -H -u 1000 -s /bin/bash www-data -G www-data
USER www-data

COPY /opt/container/nginx/nginx.conf /etc/nginx/nginx.conf
COPY --from=assets-build /var/www/html/public /var/www/html/
