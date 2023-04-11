FROM php:7.4-fpm


RUN apt update && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev libpng-dev zip wget \
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

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

