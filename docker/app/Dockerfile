 FROM php:8.0

# Optional, force UTC as server time
RUN echo "UTC" > /etc/timezone

RUN apt-get update -y
RUN apt-get install -y autoconf zlib1g-dev

# Install PHP modules
RUN pecl install grpc > /dev/null
RUN docker-php-ext-enable grpc > /dev/null

RUN pecl install protobuf > /dev/null

# Workaround solution for installing ext-sockets for PHP 8.0
# See https://github.com/docker-library/php/issues/1245
RUN CFLAGS="$CFLAGS -D_GNU_SOURCE" docker-php-ext-install sockets > /dev/null

RUN curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer

# install dependencies
RUN apt-get update -yqq > /dev/null && apt-get install -yqq git unzip > /dev/null

RUN apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

RUN docker-php-ext-enable pdo_pgsql

WORKDIR /app

EXPOSE 8080
EXPOSE 9001

CMD php app.php configure && php ./vendor/bin/rr get && ./rr serve
