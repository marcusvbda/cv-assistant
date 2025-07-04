FROM php:8.3-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl libpng-dev libonig-dev libxml2-dev libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl pdo_mysql zip exif pcntl bcmath gd

COPY ./000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 80

CMD composer install --no-interaction --prefer-dist --optimize-autoloader && \
    php artisan migrate --force && \
    php artisan db:seed --force && \
    apache2-foreground
