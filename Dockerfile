FROM php:7.1-cli

WORKDIR /www
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    git
RUN docker-php-ext-install zip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer
