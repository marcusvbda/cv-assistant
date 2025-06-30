# Dockerfile
FROM php:8.2-fpm

# Instala extensões e dependências do sistema
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    libzip-dev unzip libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www

# Copia os arquivos da aplicação
COPY . .

# Garante que dependências de PHP sejam instaladas mesmo se `composer.lock` estiver ausente
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-scripts || true

# Garante permissões corretas
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expõe a porta do servidor embutido
EXPOSE 8000

# Comando de inicialização do container
CMD php artisan migrate --force \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan storage:link \
    && php artisan serve --host=0.0.0.0 --port=8000
