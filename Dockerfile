FROM php:8.2-cli
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy composer.json first and install deps
COPY composer.json ./
RUN composer install --no-dev --prefer-dist --no-interaction

# Now copy the rest of the app
COPY . .

ENV PORT=10000
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT}"]

