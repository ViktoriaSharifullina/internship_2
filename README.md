# Описание

Упрощенный сервис с регистрацией и авторизацией.

## Технологический стек

- Php
- Laravel
- Docker
- PostgresSQL
- Nginx

## Требования

Перед началом работы убедитесь, что у вас установлены:

- Docker
- Docker Compose

## Запуск проекта

1. Клонируйте репозиторий:
   ```bash
   git clone git@github.com:ViktoriaSharifullina/internship_2.git
   cd internship_2

2. Соберите, создайте и запустите контейнер:
   ```bash
   docker-compose build
   docker-compose up -d

3. Выполните миграции базы данных:
   ```bash
   docker-compose exec app php artisan migrate

## Swagger документация

1. Сгенерируйте документацию:
   ```bash
   docker-compose exec app php artisan l5-swagger:generate

2. Перейдите по адресу
    ```bash
    http://localhost/api/documentation
    ```
