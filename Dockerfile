FROM php:8.2-cli

# Tools for Composer + PHP zip extension
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libzip-dev \
 && docker-php-ext-configure zip \
 && docker-php-ext-install zip \
 && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# 1) Install deps from composer.json
COPY composer.json ./
RUN composer install --no-dev --prefer-dist --no-interaction

# 2) Copy ONLY app files (no "COPY . .")
COPY click.php config.php create-checkout.php webhook.php ./

# 3) Start PHP server (Render provides $PORT)
ENV PORT=10000
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT}"]

