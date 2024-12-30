FROM php:8-apache as php

ARG USER_ID
ARG GROUP_ID

RUN groupadd -f -g $GROUP_ID user
RUN useradd -u $USER_ID -g $GROUP_ID user

EXPOSE 80
WORKDIR /var/www

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update -yqq
RUN apt-get upgrade -yqq
RUN apt-get install -yqq libjpeg-dev libpng-dev libbz2-dev libxslt-dev libsodium-dev libzip-dev libxslt-dev wget openssh-client zip

RUN docker-php-ext-install bz2 intl pcntl sodium zip xsl gd
RUN docker-php-ext-enable bz2 intl pcntl sodium zip xsl gd 

RUN apt-get clean
RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN a2enmod rewrite

## Php conf
COPY php.ini /usr/local/etc/php/conf.d/app.ini
COPY vhost.conf /etc/apache2/sites-available/000-default.conf
COPY apache.conf /etc/apache2/conf-available/app.conf
RUN a2enconf app

USER user
