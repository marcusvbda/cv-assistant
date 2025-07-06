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

# Copiar arquivos da aplicação
COPY . .

# Dá permissão para o usuário www-data nas pastas necessárias
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Define o usuário para rodar os processos (opcional, dependendo do seu setup)
USER www-data

# Copiar arquivos de configuração
COPY .env .env

# Instalar dependências PHP
RUN composer install --no-dev --optimize-autoloader

# Instalar frontend
RUN yarn install && yarn build

# Permissões
RUN chmod -R 775 storage bootstrap/cache

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
