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

    stages {
        stage('build-tag') {
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
            steps {
                withKubeConfig([credentialsId: 'k3s-kubeconfig']) {
                    'kubectl apply -f `pwd`/opt/kubernetes/config-map.yaml -f `pwd`/opt/kubernetes/secrets.yaml'
                    "APP_VERSION=${GIT_COMMIT} envsubst < `pwd`/opt/kubernetes/todo-app.yaml | kubectl apply -f -"
                    'kubectl apply -f `pwd`/opt/kubernetes/todo-app-svc.yaml -f `pwd`/opt/kubernetes/todo-app-ingress.yaml'
                }
            }
        }
    }
}
