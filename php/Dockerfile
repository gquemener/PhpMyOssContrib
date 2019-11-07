FROM php:rc-cli-alpine3.10

ENV PHPIZE_DEPS \
        autoconf \
        cmake \
        file \
        g++ \
        gcc \
        libc-dev \
        pcre-dev \
        make \
        git \
        pkgconf \
        re2c

RUN apk add --no-cache --virtual .persistent-deps postgresql-dev
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS
RUN docker-php-ext-configure pdo_pgsql --with-pgsql
RUN docker-php-ext-install -j$(nproc) pdo_pgsql
RUN pecl -d preferred_state=beta install xdebug
RUN apk del .build-deps && rm -rf /tmp/*
COPY config/xdebug-cli.ini /usr/local/etc/php/conf.d/
