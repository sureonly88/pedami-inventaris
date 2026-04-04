#!/bin/sh

set -e

echo "Menjalankan php artisan migrate..."
php artisan migrate --force

echo "Memulai service..."
exec "$@"
