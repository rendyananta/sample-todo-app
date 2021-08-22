FROM node:15.5-alpine AS assets-build

LABEL org.opencontainers.image.source=https://github.com/rendyananta/sample-todo-app

WORKDIR /var/www/html

COPY . /var/www/html/

RUN npm ci
RUN npm run production

FROM nginx:1.19-alpine AS nginx

COPY /opt/container/nginx/nginx.conf /etc/nginx/nginx.conf
COPY --from=assets-build /var/www/html/public /var/www/html
