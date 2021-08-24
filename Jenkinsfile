pipeline {
    agent none

    stages {
        stage('test') {
            agent 'php-mysql'

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
            agent 'kaniko'

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
