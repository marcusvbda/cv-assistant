# Use imagem oficial do PHP com FPM
FROM php:8.2-fpm

# Instala dependências do sistema
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    libpq-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define diretório de trabalho
WORKDIR /var/www

# Copia os arquivos do projeto para dentro do container
COPY . .

# Instala dependências PHP com Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --verbose

# Define permissões corretas para Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Gera caches do Laravel
RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache \
 && php artisan storage:link

# Expõe a porta 8000
EXPOSE 8000

# Inicia o servidor do Laravel (apenas para desenvolvimento ou Render)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
