#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
    cp .env.example .env
fi

if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    php artisan key:generate --force --no-interaction
fi

if ! grep -q "^JWT_SECRET=" .env || grep -q "^JWT_SECRET=$" .env; then
    php artisan jwt:secret --force --no-interaction 2>/dev/null || true
fi

if [ "$1" = "php-fpm" ]; then
    php artisan migrate --force --no-interaction
    php artisan db:seed --force --no-interaction
fi

exec "$@"
