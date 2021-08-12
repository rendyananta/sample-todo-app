pipeline {
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

    stages {

        stage('checkout') {
            steps {
                git branch: 'main', 
                    url: 'https://github.com/rendyananta/sample-todo-app.git'
            }
        }

        stage('build-php') {
            steps {
                container(name: 'kaniko', shell: '/busybox/sh') {
                    sh '''#!/busybox/sh
                        /kaniko/executor --dockerfile `pwd`/opt/container/php-fpm/Dockerfile --context `pwd` --destination rendyananta/sample-todo-app:fpm
                    '''
                }
            }
        }

        stage('build-nginx') {
            steps {
                container(name: 'kaniko', shell: '/busybox/sh') {
                    sh '''#!/busybox/sh
                        /kaniko/executor --dockerfile `pwd`/opt/container/nginx/Dockerfile --context `pwd` --destination rendyananta/sample-todo-app:nginx
                    '''
                }
            }
        }
    }

    // stages {
    //     stage('Build') {
    //         steps {
    //             git branch: 'main',
    //                 url: 'https://github.com/rendyananta/sample-todo-app.git'

    //             script {
    //                 sh 'docker --version'
    //                 // docker.build("rendyananta/sample-todo-app:fpm", "-f opt/container/php-fpm/Dockerfile")
    //             }

    //             // script {
    //             //     def app = docker.build("rendyananta/sample-todo-app:fpm", "-f opt/container/php-fpm/Dockerfile")

    //             //     def nginx = docker.build("rendyananta/sample-todo-app:nginx", "-f opt/container/nginx/Dockerfile")
    //             // }
    //         }
    //     }
    //     // stage('Test') {
    //     //     steps {
    //     //         script {
    //     //             app.inside {
    //     //                 sh 'php artisan test'
    //     //             }
    //     //         }
    //     //     }
    //     // }
    // }
}
