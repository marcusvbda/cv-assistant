FROM php:8.3-apache

WORKDIR /var/www/html

# Instalar dependências PHP + Node + Yarn (via repo oficial)
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl libpng-dev libonig-dev libxml2-dev libicu-dev \
    nodejs curl gnupg2 \
    && curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - \
    && echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list \
    && apt-get update && apt-get install -y yarn \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl pdo_mysql zip exif pcntl bcmath gd

# Config Apache
COPY ./000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar o código da aplicação
COPY . .

# Permissões necessárias para storage e cache
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Instalar dependências PHP e JS e rodar build front-end
RUN composer install --no-dev --optimize-autoloader
RUN yarn install
RUN yarn build

EXPOSE 80

CMD ["apache2-foreground"]
