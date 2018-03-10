#!/bin/bash

cd "$( dirname "${BASH_SOURCE[0]}" )"

NAME=php-spider
IMAGE=php:5.6-cli

# env

docker run -it --rm \
--name=$NAME \
-v $(pwd):/app \
$IMAGE $1