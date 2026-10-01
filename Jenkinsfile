pipeline {

    agent {
        label 'docker-agent'
    }

    environment {

        DOCKERHUB_USERNAME = 'YOUR_DOCKERHUB_USERNAME'

        BACKEND_IMAGE =
            "${DOCKERHUB_USERNAME}/sql-injection-demo-backend"

        FRONTEND_IMAGE =
            "${DOCKERHUB_USERNAME}/sql-injection-demo-frontend"

        DB_IMAGE =
            "${DOCKERHUB_USERNAME}/sql-injection-demo-db"

        DOCKER_CREDENTIALS =
            'dockerhub-credentials'
    }

    stages {

        /*
         * ==============================================
         * 1. CHECKOUT
         * ==============================================
         */

        stage('Checkout Source Code') {

            steps {

                checkout scm

                sh '''
                    echo "Git commit:"
                    git rev-parse HEAD
                '''
            }
        }


        /*
         * ==============================================
         * 2. TEST
         * ==============================================
         */

        stage('Install Dependencies and Run Tests') {

            steps {

                sh '''
                    set -e

                    echo "Starting application..."

                    docker compose up -d --build

                    echo "Waiting for backend..."

                    sleep 10

                    echo "Running tests..."

                    chmod +x backend/tests/test_endpoints.sh

                    ./backend/tests/test_endpoints.sh
                '''
            }
        }


        /*
         * ==============================================
         * 3. BUILD
         * ==============================================
         */

        stage('Build Docker Images') {

            steps {

                sh '''
                    set -e

                    echo "Building backend..."

                    docker build \
                        -t ${BACKEND_IMAGE}:${GIT_COMMIT} \
                        ./backend

                    echo "Building frontend..."

                    docker build \
                        -t ${FRONTEND_IMAGE}:${GIT_COMMIT} \
                        ./frontend

                    echo "Building database..."

                    docker build \
                        -t ${DB_IMAGE}:${GIT_COMMIT} \
                        ./database
                '''
            }
        }


        /*
         * ==============================================
         * 4. SHOW TAG
         * ==============================================
         */

        stage('Tag Images') {

            steps {

                sh '''
                    set -e

                    echo "Git commit ID:"
                    echo ${GIT_COMMIT}

                    docker images | grep sql-injection-demo
                '''
            }
        }


        /*
         * ==============================================
         * 5. PUSH
         * ==============================================
         */

        stage('Push Images to Docker Hub') {

            steps {

                withCredentials([
                    usernamePassword(
                        credentialsId: "${DOCKER_CREDENTIALS}",
                        usernameVariable: 'DOCKER_USER',
                        passwordVariable: 'DOCKER_PASSWORD'
                    )
                ]) {

                    sh '''
                        set -e

                        echo "$DOCKER_PASSWORD" | \
                        docker login \
                            -u "$DOCKER_USER" \
                            --password-stdin

                        docker push ${BACKEND_IMAGE}:${GIT_COMMIT}

                        docker push ${FRONTEND_IMAGE}:${GIT_COMMIT}

                        docker push ${DB_IMAGE}:${GIT_COMMIT}

                        docker logout
                    '''
                }
            }
        }
    }


    /*
     * ==============================================
     * CLEANUP
     * ==============================================
     */

    post {

        always {

            sh '''
                docker compose down || true
            '''

        }

        success {

            echo 'Pipeline completed successfully.'
        }

        failure {

            echo 'Pipeline failed. Images were not pushed if failure occurred before the Push stage.'
        }
    }
}
