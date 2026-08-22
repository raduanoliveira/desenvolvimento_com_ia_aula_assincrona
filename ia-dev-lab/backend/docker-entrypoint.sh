#!/bin/sh
set -e

if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist --no-security-blocking
fi

if [ ! -f .env ]; then
  cp .env.example .env
  php artisan key:generate --force
fi

mkdir -p database
touch database/database.sqlite
php artisan migrate --force

exec php artisan serve --host=0.0.0.0 --port=8000
