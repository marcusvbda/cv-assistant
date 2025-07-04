#!/bin/bash

composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan db:seed --force

# Depois inicia o nginx e php-fpm
php-fpm -D
nginx -g "daemon off;"
