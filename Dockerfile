FROM richarvey/nginx-php-fpm:3.1.6

# Copia o projeto todo para dentro do container
COPY . /var/www/html

# Copia a pasta scripts para /scripts dentro do container (confirme se scripts está na raiz)
COPY ./scripts /scripts

# Copia o start.sh para raiz do container
COPY start.sh /start.sh

# Dá permissão de execução para os scripts
RUN chmod +x /start.sh /scripts/00-laravel-scripts

ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["/start.sh"]
