FROM thecodingmachine/php:8.1-v4-apache-node16

ENV APP_ENV=dev \
    APACHE_DOCUMENT_ROOT=/var/www/html/public/


COPY composer.json /var/www/html/

COPY --chown=docker:docker . /var/www/html/

RUN composer install --ignore-platform-reqs

RUN php artisan optimize:clear
RUN php artisan config:clear
RUN php artisan cache:clear
RUN php artisan route:clear