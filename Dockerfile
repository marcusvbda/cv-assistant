FROM php:8.3-fpm

# Atualiza e instala dependências do sistema via apt-get
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    zip \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    nodejs \
    npm \
    curl \
    bash \
    build-essential \
    netcat-openbsd \
    && rm -rf /var/lib/apt/lists/*

# Instala yarn globalmente via npm
RUN npm install -g yarn

# Configura e instala extensões PHP necessárias
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) intl pdo pdo_mysql zip bcmath gd

# Instala Composer copiando do container oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Define diretório de trabalho
WORKDIR /var/www

# Copia o projeto para dentro do container
COPY . .

# Ajusta permissões
RUN chown -R www-data:www-data /var/www

# Copia o script de entrada
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Comando de inicialização
CMD ["/entrypoint.sh"]
