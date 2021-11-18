build-and-push: build-fpm build-nginx push-fpm push-nginx

build-local:
	docker build -t sample/todo-app-fpm -f opt/container/php-fpm/Dockerfile .
	docker build -t sample/todo-app-nginx -f opt/container/nginx/Dockerfile .

build-fpm:
	docker build -t ghcr.io/rendyananta/sample-todo-app:fpm -f opt/container/php-fpm/Dockerfile .

build-nginx:
	docker build -t ghcr.io/rendyananta/sample-todo-app:nginx -f opt/container/nginx/Dockerfile .

push-fpm:
	docker push ghcr.io/rendyananta/sample-todo-app:fpm

push-nginx:
	docker push ghcr.io/rendyananta/sample-todo-app:nginx

rebuild:
	docker-compose build --no-cache

run-fpm:
	docker-compose up -d && docker-compose run fpm ash

run-nginx:
	docker-compose up -d && docker-compose run nginx ash