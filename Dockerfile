FROM php:8.2-cli
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# 1. Copie ton fichier composer.json
COPY composer.json ./

# 2. Installe les dépendances Stripe
RUN composer install --no-dev --prefer-dist --no-interaction

# 3. Copie le reste du projet (tes fichiers PHP)
COPY . .

# 4. Lance le serveur PHP interne sur Render
ENV PORT=10000
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT}"]
