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
                    script {
                        try {
                            sh """#!/bin/bash
                              php artisan key:generate --env=testing
                              php artisan test --env=testing
                            """
                        } catch (err) {
                            error('Build aborted. Reason: Cannot pass unit tests')
                        }
                    }
                }
                
                // UI test
                container(name: 'php', shell: '/bin/bash') {
                    script {
                        try {
                            sh """#!/bin/bash
                              cp .env.example .env
                              configure-laravel
                              start-nginx-ci-project

                              npm ci
                              npm run prod

                              php artisan dusk
                            """
                        } catch (err) {
                            error('Build aborted. Reason: Cannot pass UI tests')
                        }
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
"""
                }
            }

            steps {
                container(name: 'kaniko', shell: '/busybox/sh') {
                  script {
                        try {
                            sh """#!/busybox/sh
                            /kaniko/executor --dockerfile=`pwd`/opt/container/php-fpm/Dockerfile --context=`pwd` --destination=rendyananta/sample-todo-app:fpm-${GIT_COMMIT} --cache --cache-dir=/cache --cache-copy-layers
                            /kaniko/executor --dockerfile=`pwd`/opt/container/nginx/Dockerfile --context=`pwd` --destination=rendyananta/sample-todo-app:nginx-${GIT_COMMIT} --cache --cache-dir=/cache --cache-copy-layers
                            """
                        } catch (err) {
                            error('Build aborted. Reason: Cannot build docker image')
                        }
                    }
                }
            }
        }

        stage('deployment') {
            parallel {
                stage('deploy') {
                    when { branch 'main' }
                    agent any
                    steps {
                        withKubeConfig([credentialsId: 'target-kubeconfig']) {
                            sh """
                            kubectl apply -f `pwd`/opt/kubernetes/config-map.yaml -f `pwd`/opt/kubernetes/secrets.yaml
                            sops --pgp ${SOP_PGP_FP} -d `pwd`/opt/kubernetes/secrets.enc.yaml | kubectl apply -f
                            APP_VERSION=${GIT_COMMIT} envsubst < `pwd`/opt/kubernetes/todo-app.yaml | kubectl apply -f -
                            kubectl apply -f `pwd`/opt/kubernetes/todo-app-svc.yaml -f `pwd`/opt/kubernetes/todo-app-ingress.yaml
                            """
                        }
                    }
                }

                stage('code-analysis') {
                    agent any
                    steps {
                        script {
                            def sqScannerMsBuildHome = tool 'scanner-4.6'
                            withSonarQubeEnv('sonar-server') {
                                sh "${sqScannerMsBuildHome}/bin/sonar-scanner -Dsonar.login=${SONAR_AUTH_TOKEN} -Dsonar.projectKey=laravel-sample-todo"
                            }
                        }
                    }
                }
            }
        }
    }
}
