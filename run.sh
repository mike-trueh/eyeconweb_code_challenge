#!/bin/sh

cp .env.example .env

docker-compose up -d

docker-compose exec laravel.test composer install

./vendor/bin/sail artisan key:generate

./vendor/bin/sail artisan migrate
