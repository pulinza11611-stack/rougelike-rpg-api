#!/usr/bin/env bash
set -e

cd /var/www

PORT="${PORT:-8080}"
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Runtime MPM fix: ensure only mpm_prefork is enabled (build-time fix may be cached)
echo "[start-railway] mpm symlinks before cleanup:"
ls -la /etc/apache2/mods-enabled/ | grep mpm || echo "  (none)"
rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf
ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load
ln -sf /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf
echo "[start-railway] mpm symlinks after cleanup:"
ls -la /etc/apache2/mods-enabled/ | grep mpm

if [ -z "${APP_KEY}" ]; then
    echo "[start-railway] WARNING: APP_KEY is empty. Generate one with 'php artisan key:generate --show' and set it in Railway Variables."
fi

# Sanitize APP_URL: if RAILWAY_PUBLIC_DOMAIN is empty, APP_URL becomes "https://" which Laravel rejects (Invalid URI).
case "${APP_URL}" in
    "https://"|"http://"|"")
        export APP_URL="http://localhost"
        echo "[start-railway] APP_URL was empty/invalid, falling back to ${APP_URL}"
        ;;
esac

echo "[start-railway] running migrations..."
php artisan migrate --force || echo "[start-railway] migrate failed (continuing)"

php artisan config:cache || true
php artisan route:cache || true

echo "[start-railway] starting Apache on port ${PORT}"
exec apache2-foreground
