pipeline {

    /*
     * Jenkins build agent
     */
    agent {
        label 'docker-agent'
    }


    /*
     * Parameters shown in:
     *
     * Build with Parameters
     */
    parameters {

        choice(
            name: 'SERVICE',
            choices: [
                'frontend',
                'backend',
                'db',
                'all'
            ],
            description: 'Select the service to build and push'
        )
    }


    /*
     * Environment variables
     */
    environment {

        DOCKERHUB_USERNAME = 'maanav22'

        FRONTEND_IMAGE = "${DOCKERHUB_USERNAME}/sql-injection-demo-frontend"

        BACKEND_IMAGE = "${DOCKERHUB_USERNAME}/sql-injection-demo-backend"

        DB_IMAGE = "${DOCKERHUB_USERNAME}/sql-injection-demo-db"

        DOCKER_CREDENTIALS = 'dockerhub-credentials'
    }


    stages {


        /*
         * =====================================================
         * 1. CHECKOUT
         * =====================================================
         */

        stage('Checkout Source Code') {

            steps {

                checkout scm

                sh '''
                    echo "======================================"
                    echo "Git Commit"
                    echo "======================================"

                    git rev-parse HEAD
                '''
            }
        }


        /*
         * =====================================================
         * 2. RUN TESTS
         * =====================================================
         */

        stage('Install Dependencies and Run Tests') {

            steps {

                sh '''
                    set -e

                    echo "======================================"
                    echo "Starting application for testing"
                    echo "======================================"

                    docker compose up -d db backend

                    echo "Waiting for backend..."

                    sleep 10

                    echo "======================================"
                    echo "Running tests"
                    echo "======================================"

                    chmod +x backend/tests/test_endpoints.sh

                    ./backend/tests/test_endpoints.sh

                    echo "======================================"
                    echo "Tests passed"
                    echo "======================================"
                '''
            }
        }


        /*
         * =====================================================
         * 3. BUILD FRONTEND
         * =====================================================
         */

        stage('Build Frontend') {

            when {

                expression {

                    params.SERVICE == 'frontend' ||
                    params.SERVICE == 'all'

                }
            }

            steps {

                sh '''
                    set -e

                    echo "Building frontend image..."

                    docker build \
                        -t ${FRONTEND_IMAGE}:${GIT_COMMIT} \
                        ./frontend

                    echo "Frontend image built successfully."
                '''
            }
        }


        /*
         * =====================================================
         * 4. BUILD BACKEND
         * =====================================================
         */

        stage('Build Backend') {

            when {

                expression {

                    params.SERVICE == 'backend' ||
                    params.SERVICE == 'all'

                }
            }

            steps {

                sh '''
                    set -e

                    echo "Building backend image..."

                    docker build \
                        -t ${BACKEND_IMAGE}:${GIT_COMMIT} \
                        ./backend

                    echo "Backend image built successfully."
                '''
            }
        }


        /*
         * =====================================================
         * 5. BUILD DATABASE
         * =====================================================
         */

        stage('Build Database') {

            when {

                expression {

                    params.SERVICE == 'db' ||
                    params.SERVICE == 'all'

                }
            }

            steps {

                sh '''
                    set -e

                    echo "Building database image..."

                    docker build \
                        -t ${DB_IMAGE}:${GIT_COMMIT} \
                        ./database

                    echo "Database image built successfully."
                '''
            }
        }


        /*
         * =====================================================
         * 6. PUSH FRONTEND
         * =====================================================
         */

        stage('Push Frontend') {

            when {

                expression {

                    params.SERVICE == 'frontend' ||
                    params.SERVICE == 'all'

                }
            }

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

                        echo "Logging into Docker Hub..."

                        echo "$DOCKER_PASSWORD" | \
                        docker login \
                            --username "$DOCKER_USER" \
                            --password-stdin

                        echo "Pushing frontend..."

                        docker push \
                            ${FRONTEND_IMAGE}:${GIT_COMMIT}

                        docker logout
                    '''
                }
            }
        }


        /*
         * =====================================================
         * 7. PUSH BACKEND
         * =====================================================
         */

        stage('Push Backend') {

            when {

                expression {

                    params.SERVICE == 'backend' ||
                    params.SERVICE == 'all'

                }
            }

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

                        echo "Logging into Docker Hub..."

                        echo "$DOCKER_PASSWORD" | \
                        docker login \
                            --username "$DOCKER_USER" \
                            --password-stdin

                        echo "Pushing backend..."

                        docker push \
                            ${BACKEND_IMAGE}:${GIT_COMMIT}

                        docker logout
                    '''
                }
            }
        }


        /*
         * =====================================================
         * 8. PUSH DATABASE
         * =====================================================
         */

        stage('Push Database') {

            when {

                expression {

                    params.SERVICE == 'db' ||
                    params.SERVICE == 'all'

                }
            }

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

                        echo "Logging into Docker Hub..."

                        echo "$DOCKER_PASSWORD" | \
                        docker login \
                            --username "$DOCKER_USER" \
                            --password-stdin

                        echo "Pushing database..."

                        docker push \
                            ${DB_IMAGE}:${GIT_COMMIT}

                        docker logout
                    '''
                }
            }
        }


        /*
         * =====================================================
         * 9. SHOW BUILD INFORMATION
         * =====================================================
         */

        stage('Build Information') {

            steps {

                sh '''
                    echo "======================================"
                    echo "BUILD INFORMATION"
                    echo "======================================"

                    echo "Selected Service:"
                    echo "${SERVICE}"

                    echo ""

                    echo "Git Commit:"
                    echo "${GIT_COMMIT}"

                    echo ""

                    echo "Images:"
                    docker images | grep sql-injection-demo || true
                '''
            }
        }
    }


    /*
     * =========================================================
     * POST ACTIONS
     * =========================================================
     */

    post {

        always {

            echo "Cleaning up test containers..."

            sh '''
                docker compose down || true
            '''
        }


        success {

            echo "======================================"
            echo "PIPELINE SUCCESS"
            echo "======================================"

            echo "Service: ${SERVICE}"

            echo "Git Commit: ${GIT_COMMIT}"
        }


        failure {

            echo "======================================"
            echo "PIPELINE FAILED"
            echo "======================================"

            echo "If tests failed, Docker images were not pushed."
        }
    }
}
