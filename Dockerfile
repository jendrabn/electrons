# syntax=docker/dockerfile:1.6

FROM node:20-alpine AS frontend
WORKDIR /var/www/electrons

RUN apk add --no-cache libc6-compat python3 make g++

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build

FROM php:8.3-fpm-bookworm AS php

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    PHP_OPCACHE_VALIDATE_TIMESTAMPS=0 \
    PHP_OPCACHE_MAX_ACCELERATED_FILES=20000 \
    PHP_OPCACHE_MEMORY_CONSUMPTION=256 \
    PHP_OPCACHE_ENABLE=1 \
    PHP_OPCACHE_ENABLE_CLI=1

RUN apt-get update \
 && apt-get install -y --no-install-recommends \
    build-essential \
    ca-certificates \
    curl \
    git \
    pkg-config \
    unzip \
    libicu-dev \
    libzip-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libfreetype6-dev \
    libgmp-dev \
    libonig-dev \
    libsqlite3-dev \
    libsodium-dev \
    libssl-dev \
    libxml2-dev \
    zlib1g-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
 && docker-php-ext-install -j"$(nproc)" \
    bcmath \
    exif \
    gd \
    intl \
    mbstring \
    mysqli \
    opcache \
    pcntl \
    pdo_mysql \
    pdo_sqlite \
    zip \
 && pecl install redis \
 && docker-php-ext-enable redis \
 && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/electrons

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts \
 && rm -rf /root/.composer

COPY . .
COPY --from=frontend /var/www/electrons/public/build ./public/build

RUN rm -rf node_modules \
 && mkdir -p storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
 && ln -sfn ../storage/app/public public/storage \
 && touch storage/logs/laravel.log \
 && chown -R www-data:www-data storage bootstrap/cache public/storage

USER www-data

EXPOSE 9000

CMD ["php-fpm"]

FROM nginx:1.27-alpine AS nginx

WORKDIR /var/www/electrons

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=php /var/www/electrons/public /var/www/electrons/public

RUN mkdir -p /var/www/electrons/storage/app/public \
 && ln -sfn ../storage/app/public /var/www/electrons/public/storage
