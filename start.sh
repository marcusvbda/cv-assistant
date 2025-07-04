#!/bin/sh

echo "Running Laravel deploy script..."
sh /scripts/00-laravel-scripts

echo "Starting nginx + php-fpm..."
exec /start.sh
