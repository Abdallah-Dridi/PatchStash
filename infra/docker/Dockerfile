FROM php:8.2-fpm

# Core system dependencies required by the app and PHP extensions
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        curl \
        netcat-openbsd \
        libicu-dev \
        libzip-dev \
        libonig-dev \
        libxml2-dev \
        libpq-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        mariadb-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql intl zip opcache gd \
    && rm -rf /var/lib/apt/lists/*

# Optional but handy: Symfony CLI for local tooling
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# PHP configuration
COPY infra/docker/php.ini /usr/local/etc/php/php.ini

# Composer (from the official image for repeatable builds)
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_CACHE_DIR=/tmp/composer \
    PATH="/var/www/html/vendor/bin:${PATH}"

WORKDIR /var/www/html

# Lightweight entrypoint to keep dev experience smooth
RUN mkdir -p /tmp/composer \
    && chown -R www-data:www-data /tmp/composer \
    && cat > /usr/local/bin/patchstash-entrypoint <<'EOF'
#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

mkdir -p var/cache var/log
chown -R www-data:www-data var || true

DB_HOST="${MYSQL_HOST:-mysql}"
DB_NAME="${MYSQL_DATABASE:-patchstash}"
DB_USER="${MYSQL_USER:-root}"
DB_PASS="${MYSQL_PASSWORD:-root}"

echo "Waiting for MySQL at ${DB_HOST}..."
until php -r "new PDO('mysql:host=${DB_HOST};dbname=${DB_NAME}', '${DB_USER}', '${DB_PASS}');" >/dev/null 2>&1; do
  sleep 1
done

if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist --no-progress
fi

if [ -d vendor ]; then
  chown -R www-data:www-data vendor || true
fi

if [ "${AUTO_MIGRATE:-1}" = "1" ]; then
  php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
fi

if [ "${AUTO_WARM_CACHE:-1}" = "1" ]; then
  php bin/console cache:warmup --no-optional-warmers
fi

exec php-fpm
EOF

RUN chmod +x /usr/local/bin/patchstash-entrypoint

ENTRYPOINT ["/usr/local/bin/patchstash-entrypoint"]
