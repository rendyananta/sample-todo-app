pipeline {
    agent none

    stages {
        stage('test') {
            agent {
                kubernetes {
                    yaml """
kind: Pod
metadata:
  name: php
spec:
  containers:
    - name: composer
      image: composer:2
      imagePullPolicy: Always
      command:
        - sleep
      args:
        - 9999999
    - name: php
      image: chilio/laravel-dusk-ci:php-8.0
      imagePullPolicy: Always
      env:
        - name: APP_URL
          value: http://localhost
        - name: DB_CONNECTION
          value: mysql
        - name: DB_HOST
          value: 127.0.0.1
        - name: DB_PORT
          value: 3306
        - name: DB_DATABASE
          value: laravel
        - name: DB_USERNAME
          value: laravel
        - name: DB_PASSWORD
          value: laravel
    - name: mariadb
      image: mariadb:latest
      env:
        - name: MYSQL_RANDOM_ROOT_PASSWORD
          value: "true"
        - name: MYSQL_USER
          value: laravel
        - name: MYSQL_PASSWORD
          value: laravel
        - name: MYSQL_DATABASE
          value: laravel
      resources: {}
      ports:
        - containerPort: 3306
                """
                }
            }

            steps {
                // 
                container(name: 'composer', shell: '/bin/ash') {
                    catchError {
                        sh """#!/bin/ash
                          composer install --prefer-dist --no-ansi
                        """
                    }
                }

                // Unit test
                container(name: 'php', shell: '/bin/bash') {
                    catchError () {
                        sh """#!/bin/bash
                          php artisan test --env=testing
                        """
                    }
                }
                
                // UI test
                container(name: 'php', shell: '/bin/bash') {
                    catchError {
                        sh """#!/bin/bash
                          cp .env.example .env
                          configure-laravel
                          start-nginx-ci-project

                          npm ci
                          npm run prod

                          php artisan dusk
                        """
                    }
                }
            }
        }
      
        stage('build') {
            agent {
                kubernetes {
                    yaml """
kind: Pod
metadata:
  name: kaniko
spec:
  containers:
  - name: kaniko
    image: gcr.io/kaniko-project/executor:debug
    imagePullPolicy: Always
    command:
      - sleep
    args:
      - 9999999
    volumeMounts:
    - name: jenkins-docker-cfg
      mountPath: /kaniko/.docker
    - name: kaniko-cache
      mountPath: /cache
  volumes:
  - name: jenkins-docker-cfg
    projected:
      sources:
      - secret:
          name: docker-credentials
          items:
            - key: .dockerconfigjson
              path: config.json
  - name: kaniko-cache
    persistentVolumeClaim: 
      claimName: kaniko-cache
"""
                }
            }

            steps {
                container(name: 'kaniko', shell: '/busybox/sh') {
                    sh """#!/busybox/sh
                        /kaniko/executor --dockerfile=`pwd`/opt/container/php-fpm/Dockerfile --context=`pwd` --destination=rendyananta/sample-todo-app:fpm-${GIT_COMMIT} --cache --cache-dir=/cache --cache-copy-layers
                        /kaniko/executor --dockerfile=`pwd`/opt/container/nginx/Dockerfile --context=`pwd` --destination=rendyananta/sample-todo-app:nginx-${GIT_COMMIT} --cache --cache-dir=/cache --cache-copy-layers
                    """
                }
            }
        }

        stage('deploy') {
            when { branch 'main' }
            agent any
            steps {
                withKubeConfig([credentialsId: 'k3s-kubeconfig']) {
                    sh """
                    kubectl apply -f `pwd`/opt/kubernetes/config-map.yaml -f `pwd`/opt/kubernetes/secrets.yaml
                    APP_VERSION=${GIT_COMMIT} envsubst < `pwd`/opt/kubernetes/todo-app.yaml | kubectl apply -f -
                    kubectl apply -f `pwd`/opt/kubernetes/todo-app-svc.yaml -f `pwd`/opt/kubernetes/todo-app-ingress.yaml
                    """
                }
            }
        }
    }
}
