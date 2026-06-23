FROM php:8.3-cli

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader

COPY . .

EXPOSE 8000

CMD php -S 0.0.0.0:${PORT:-8000} -t public
