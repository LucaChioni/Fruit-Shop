# =========================
# Dipendenze PHP
# =========================
FROM composer:2 AS composer

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

COPY . .

RUN composer dump-autoload \
    --no-dev \
    --optimize \
    --classmap-authoritative


# =========================
# Build frontend Vue / Vite
# =========================
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .

RUN npm run build


# =========================
# Container PHP-FPM
# =========================
FROM php:8.5-fpm-alpine AS app

WORKDIR /var/www/html

RUN apk add --no-cache \
        icu-libs \
        libzip \
        mysql-client \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev \
        libzip-dev \
        linux-headers \
    && docker-php-ext-install \
        bcmath \
        intl \
        opcache \
        pcntl \
        pdo_mysql \
        zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

COPY --from=composer /app /var/www/html
COPY --from=frontend /app/public/build /var/www/html/public/build

RUN mkdir -p \
        storage/app/public \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && ln -sfn /var/www/html/storage/app/public /var/www/html/public/storage \
    && chown -R www-data:www-data storage bootstrap/cache

USER www-data

EXPOSE 9000

# =========================
# Container Nginx interno
# =========================
FROM nginx:alpine AS web

COPY docker/production/nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=composer /app/public /var/www/html/public
COPY --from=frontend /app/public/build /var/www/html/public/build

RUN ln -sfn /var/www/html/storage/app/public /var/www/html/public/storage

EXPOSE 80

CMD ["php-fpm"]