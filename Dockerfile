# Etapa 1: Base com PHP 8.3 e dependências
FROM php:8.3-fpm as base

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libzip-dev libonig-dev libicu-dev \
    nodejs npm cron supervisor && \
    docker-php-ext-install intl
# Instalar extensões PHP
RUN docker-php-ext-install pdo pdo_pgsql mbstring zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar Yarn globalmente
RUN npm install -g yarn

# Diretório de trabalho
WORKDIR /var/www/html

# Copia todo o código para o container
COPY . /var/www/html

# Ajusta dono e permissões ANTES do composer install
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/js/filament

# (Opcional) Troca para o usuário www-data antes do composer install
USER www-data

# Instala dependências PHP
RUN composer install --no-dev --optimize-autoloader

# Volta para root se precisar fazer outras operações administrativas
USER root

# Permissões finais (se quiser reforçar depois do build)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Etapa 2: Setup de supervisord para queue e web server
FROM base as final

# Instala servidor web embutido e Supervisor
RUN echo "* * * * * www-data php /var/www/html/artisan schedule:run >> /dev/null 2>&1" >> /etc/crontab

# Copiar configuração do Supervisor
COPY ./docker/supervisord.conf /etc/supervisord.conf

# Expõe a porta que o Laravel usará
EXPOSE 8000

# Inicia supervisord (roda queue e web juntos)
CMD ["supervisord", "-c", "/etc/supervisord.conf"]
