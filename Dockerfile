FROM php:8.3-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl libpng-dev libonig-dev libxml2-dev libicu-dev \
    nodejs curl gnupg2 \
    && curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - \
    && echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list \
    && apt-get update && apt-get install -y yarn \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl pdo_mysql zip exif pcntl bcmath gd

COPY ./000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Criar usuário appuser e dar permissões
RUN useradd -m appuser && chown -R appuser:appuser /var/www/html

USER appuser

COPY --chown=appuser:appuser . .

RUN composer install --no-dev --optimize-autoloader
RUN yarn install
RUN yarn build

# Voltar para root para expor porta e iniciar apache
USER root

RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 80

CMD ["apache2-foreground"]
