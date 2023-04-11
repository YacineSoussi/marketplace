FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
    zlib1g-dev g++ git libicu-dev libzip-dev libpng-dev zip wget \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && docker-php-ext-install gd

RUN wget https://github.com/elastic/apm-agent-php/releases/download/v1.5.1/apm-agent-php_1.5.1_all.deb
RUN dpkg -i apm-agent-php_1.5.1_all.deb

COPY local.ini /usr/local/etc/php/conf.d/local.ini 

WORKDIR /var/www/MarketPlace

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get update && apt-get install -y gnupg2 \
    && curl -sS https://packages.blackfire.io/gpg.key | apt-key add - \
    && echo "deb http://packages.blackfire.io/debian any main" > /etc/apt/sources.list.d/blackfire.list \
    && apt-get update \
    && apt-get install -y blackfire-agent blackfire-php

CMD ["php-fpm"]

EXPOSE 9000
