FROM php:7.4-apache

ENV APACHE_DOCUMENT_ROOT=/vaw/www/html/public

RUN apt-get update \
    && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql

WORKDIR /var/www/html

COPY . .
COPY .docker/default.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite

EXPOSE 80

CMD /usr/sbin/apache2ctl -D FOREGROUND
