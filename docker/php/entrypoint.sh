#!/usr/bin/env bash
set -e

cd /var/www

if [ ! -f .env ]; then
    cp .env.example .env
    echo "[entrypoint] .env created from .env.example"
fi

if [ ! -d vendor ] || [ -z "$(ls -A vendor 2>/dev/null)" ]; then
    echo "[entrypoint] installing composer dependencies..."
    composer install --no-interaction --optimize-autoloader
fi

if ! grep -q "^APP_KEY=base64:" .env; then
    php artisan key:generate --force
fi

echo "[entrypoint] waiting for MySQL at ${DB_HOST}:${DB_PORT}..."
until nc -z "${DB_HOST}" "${DB_PORT}"; do
    sleep 1
done
echo "[entrypoint] MySQL is up."

php artisan migrate --force || echo "[entrypoint] migrate failed (continuing)"

chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

exec "$@"
