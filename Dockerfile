# Dockerfile
FROM php:8.2-fpm

# Установка зависимостей и очистка в одном слое
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Установка расширений PHP
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Копирование только composer файлов для кеширования зависимостей
COPY composer.json composer.lock /var/www/

# Установка рабочей директории
WORKDIR /var/www

# Установка зависимостей Composer
RUN composer install --no-scripts --no-autoloader && composer clear-cache

# Копирование приложения
COPY . /var/www

# Завершение установки Composer
RUN composer dump-autoload --optimize

# Права на папку для кеша и логов
RUN chown -R www-data:www-data /var/www/storage && chmod -R 775 /var/www/storage

CMD ["php-fpm"]
