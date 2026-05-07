#!/usr/bin/env bash
set -e

cd /var/www

PORT="${PORT:-8080}"
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

if [ -z "${APP_KEY}" ]; then
    echo "[start-railway] WARNING: APP_KEY is empty. Generate one with 'php artisan key:generate --show' and set it in Railway Variables."
fi

echo "[start-railway] running migrations..."
php artisan migrate --force || echo "[start-railway] migrate failed (continuing)"

php artisan config:cache || true
php artisan route:cache || true

echo "[start-railway] starting Apache on port ${PORT}"
exec apache2-foreground
