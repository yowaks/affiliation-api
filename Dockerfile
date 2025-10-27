FROM php:8.2-cli
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY . .
RUN composer install --no-dev --prefer-dist
ENV PORT=10000
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT}"]
