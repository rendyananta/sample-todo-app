FROM node:15.5-alpine AS assets-build

LABEL org.opencontainers.image.source=https://github.com/rendyananta/sample-todo-app

WORKDIR /var/www/html

COPY . /var/www/html/

RUN npm ci
RUN npm run production

FROM nginx:1.19-alpine AS nginx

RUN adduser -D -H -u 1000 -s /bin/bash www-data -G www-data

RUN chown -R www-data:www-data /var/cache/nginx && \
        chown -R www-data:www-data /var/log/nginx && \
        chown -R www-data:www-data /etc/nginx/conf.d && \
        chown www-data:www-data /etc/nginx/nginx.conf

# Setup nginx pid
RUN touch /var/run/nginx.pid && \
      chown -R www-data:www-data /var/run/nginx.pid

USER www-data

COPY /opt/container/nginx/nginx.conf /etc/nginx/nginx.conf
COPY --from=assets-build /var/www/html/public /var/www/html
