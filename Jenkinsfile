pipeline {
  agent any
  stages {
    stage('Build') {
      steps {
        echo 'Building..'
      }
    }

    stage('Test') {
      steps {
        echo 'Testing..'
      }
    }

    stages {

        stage('Source') {
            git url: 'https://github.com/rendyananta/sample-todo-app.git'
        }

        stage('Build') {
            steps {
                def app = docker.build("rendyananta/sample-todo-app:fpm", "-f opt/container/php-fpm/Dockerfile")

                def nginx = docker.build("rendyananta/sample-todo-app:nginx", "-f opt/container/nginx/Dockerfile")
            }
        }
        stage('Test') {
            steps {
                app.inside {
                    sh 'php artisan test'
                }
            }
        }
        // stage('Deploy') {
        //     steps {
        //         echo 'Deploying....'
        //     }
        // }
    }

  }
}