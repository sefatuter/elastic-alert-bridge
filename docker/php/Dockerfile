FROM php:8.3-fpm-alpine

# ---------- system packages ----------
RUN apk add --no-cache \
        icu-dev oniguruma-dev libxml2-dev \
        git curl zip unzip sqlite-dev

# ---------- PHP extensions ----------
RUN docker-php-ext-install intl pdo pdo_mysql pdo_sqlite mbstring xml

# ---------- Composer ----------
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# ---------- app code ----------
WORKDIR /var/www/html

# No need to copy files as they're mounted as a volume in docker-compose.yaml

CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]
