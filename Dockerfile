FROM php:8.3-apache

WORKDIR /var/www/html

# Instala dependências do sistema e PHP extensions incluindo pdo_pgsql para PostgreSQL
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl libpng-dev libonig-dev libxml2-dev libicu-dev libpq-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl pdo_pgsql zip exif pcntl bcmath gd

# Habilita mod_rewrite do Apache
RUN a2enmod rewrite

# Copia a configuração personalizada do Apache (ajuste conforme seu arquivo)
COPY ./000-default.conf /etc/apache2/sites-available/000-default.conf

# Copia o Composer do container oficial do Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia todo o código da aplicação para dentro do container
COPY . .

# Ajusta permissões para storage e cache (essenciais para Laravel)
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Expondo porta 80 para o Apache
EXPOSE 80

# Comando padrão: instala dependências, executa migrations e seed, e inicia Apache
CMD composer install --no-interaction --prefer-dist --optimize-autoloader && \
    php artisan migrate --force && \
    php artisan db:seed --force && \
    apache2-foreground
