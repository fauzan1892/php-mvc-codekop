# syntax=docker/dockerfile:1
FROM php:8.4-apache

# --- System packages + PHP extensions -------------------------------------
# libsqlite3-dev is required to compile pdo_sqlite; pdo_mysql and opcache
# need nothing extra on the Debian-based php:8.4-apache image.
RUN apt-get update && apt-get install -y --no-install-recommends \
        libsqlite3-dev \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql pdo_sqlite opcache \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-codekop.ini
COPY docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# --- Composer ---------------------------------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install dependencies first so the layer is cached while app code changes.
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --no-interaction \
        --no-progress \
        --no-scripts \
        --optimize-autoloader

# Project has no public/ directory: index.php is the front controller and
# .htaccess denies everything except it, so the whole tree is copied in.
COPY . .

RUN composer dump-autoload --no-dev --optimize \
    && mkdir -p storage/sessions storage/queue/pending storage/queue/processing storage/queue/failed \
    && chown -R www-data:www-data storage \
    && find storage -type d -exec chmod 750 {} \; \
    && find storage -type f -exec chmod 640 {} \;

EXPOSE 80
