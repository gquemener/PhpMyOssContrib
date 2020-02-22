# Install php dependencies
FROM composer as composer
ARG APP_ENV=dev
WORKDIR /root/
COPY composer.json .
COPY composer.lock .
RUN if [ "$APP_ENV" = "dev" ] ; then composer install --no-scripts ; else composer install --no-scripts --no-dev ; fi

# Install node dependencies
FROM node:lts-alpine as node
ARG APP_ENV=dev
ENV NODE_ENV=$APP_ENV
WORKDIR /root/
COPY package.json .
COPY yarn.lock .
COPY webpack.config.js .
COPY assets assets
RUN yarn install

# Build js app
RUN if [ "$APP_ENV" = "dev" ] ; then yarn run encore dev ; else yarn run encore production ; fi

# Prepare php environment
FROM php:7.4-apache as php

ARG APP_ENV=dev
ENV APP_ENV=$APP_ENV
ENV APACHE_DOCUMENT_ROOT /app/public

WORKDIR /app/
COPY --from=composer /root/vendor /app/vendor
COPY --from=node /root/node_modules /app/node_modules

RUN apt-get update && apt-get install -y --no-install-recommends libpq-dev && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-configure pdo_pgsql
RUN docker-php-ext-install -j$(nproc) pdo_pgsql pcntl opcache
RUN if [ "$APP_ENV" = "dev" ] ; then pecl -d preferred_state=beta install xdebug; fi
COPY docker/php/config/*.ini /usr/local/etc/php/conf.d/
RUN if [ "$APP_ENV" = "prod" ] ; then rm /usr/local/etc/php/conf.d/xdebug-cli.ini; fi

COPY docker/php/apache/site.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php/apache/ports.conf /etc/apache2/ports.conf
RUN a2enmod rewrite
RUN if [ "$APP_ENV" = "dev" ] ; then mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"; fi
RUN if [ "$APP_ENV" = "prod" ] ; then mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"; fi

COPY . .
COPY --from=node /root/public/build /app/public/build
RUN php bin/console cache:warmup
RUN php bin/console cache:clear
