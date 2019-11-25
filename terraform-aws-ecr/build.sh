#!bin/bash
set -e
cd build

which aws > /dev/null || { echo 'ERROR: aws-cli is not installed' ; exit 1; }

$(AWS_ACCESS_KEY_ID=$2 AWS_SECRET_ACCESS_KEY=$3 aws ecr get-login --no-include-email --region $1)

# Check that docker is installed and running
which docker > /dev/null && docker ps > /dev/null || { echo 'ERROR: docker is not running' ; exit 1; }

echo "Building $aws_ecr_repository_url_with_tag from Dockerfile"

docker build -t $4:latest .

docker push $4:latest
