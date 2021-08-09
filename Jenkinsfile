pipeline {
  agent any

    stages {
        stage('Build') {
            steps {
                git url: 'https://github.com/rendyananta/sample-todo-app.git'

                docker.build("rendyananta/sample-todo-app:fpm", "-f opt/container/php-fpm/Dockerfile")

                // script {
                //     def app = docker.build("rendyananta/sample-todo-app:fpm", "-f opt/container/php-fpm/Dockerfile")

                //     def nginx = docker.build("rendyananta/sample-todo-app:nginx", "-f opt/container/nginx/Dockerfile")
                // }
            }
        }
        // stage('Test') {
        //     steps {
        //         script {
        //             app.inside {
        //                 sh 'php artisan test'
        //             }
        //         }
        //     }
        // }
    }
}   