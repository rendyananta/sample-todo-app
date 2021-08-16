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
    persistenceVolumeClaim: 
      claimName: kaniko-cache
"""
        }
    }

    stages {
        stage('build') {
            steps {
                container(name: 'kaniko', shell: '/busybox/sh') {
                    sh '''#!/busybox/sh
                        /kaniko/executor --dockerfile `pwd`/opt/container/php-fpm/Dockerfile --context `pwd` --destination rendyananta/sample-todo-app:fpm --cache=true --cache-copy-layers
                        /kaniko/executor --dockerfile `pwd`/opt/container/nginx/Dockerfile --context `pwd` --destination rendyananta/sample-todo-app:nginx --cache=true --cache-copy-layers
                    '''
                }
            }
        }
    }
}
