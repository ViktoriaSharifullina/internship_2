FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock /var/www/

WORKDIR /var/www

RUN composer install --no-scripts --no-autoloader && composer clear-cache

COPY . /var/www

RUN composer dump-autoload --optimize

RUN chown -R www-data:www-data /var/www/storage && chmod -R 775 /var/www/storage

CMD ["php-fpm"]
