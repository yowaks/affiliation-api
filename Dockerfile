FROM php:8.2-cli

# Outils nécessaires à Composer + extension PHP zip
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libzip-dev \
 && docker-php-ext-configure zip \
 && docker-php-ext-install zip \
 && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# 1) Copier composer.json d'abord puis installer les deps
COPY composer.json ./
RUN composer install --no-dev --prefer-dist --no-interaction

# 2) Copier le reste du projet
COPY . .

# 3) Démarrer le serveur PHP (Render fournit $PORT)
ENV PORT=10000
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT}"]

