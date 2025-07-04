FROM richarvey/nginx-php-fpm:3.1.6

COPY . /var/www/html
COPY ./scripts /scripts

COPY start.sh /start.sh
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
