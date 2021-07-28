build-fpm:
	docker build -t rendyananta/todo-app opt

build-nginx:
	docker build -t rendyananta/todo-app-webserver
