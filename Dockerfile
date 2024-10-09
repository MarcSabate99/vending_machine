FROM php:8.2-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
        curl \
        libzip-dev \
        unzip \
        && docker-php-ext-install zip \
        && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
        && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY . /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
