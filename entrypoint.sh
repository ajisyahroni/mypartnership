#!/bin/sh
set -e

# Debug: Log start of entrypoint
echo "Starting entrypoint.sh" > /var/log/entrypoint.log

# Start cron service
echo "Starting cron" >> /var/log/entrypoint.log
if ! command -v cron > /dev/null; then
    echo "Error: cron not found" >> /var/log/entrypoint.log
    exit 1
fi
cron >> /var/log/entrypoint.log 2>&1 &

echo "Checking/creating storage symlink..." >> /var/log/entrypoint.log

# rm -rf /app/public/ci-media
# ln -s /var/www/html /app/public/ci-media
# echo "Symlink ci-media dibuat" >> /var/log/entrypoint.log

# rm -rf /app/public/ci-dok
# ln -s /var/www/html/dok /app/public/ci-dok
# echo "Symlink ci-dok dibuat" >> /var/log/entrypoint.log


# Hapus folder public/storage lama
rm -rf /app/public/storage
echo "Folder public/storage dihapus" >> /var/log/entrypoint.log

# Buat symlink baru
if ! ln -s /app/storage/app/public /app/public/storage >> /var/log/entrypoint.log 2>&1; then
    echo "Error: gagal membuat symlink public/storage" >> /var/log/entrypoint.log
    exit 1
fi
echo "Symlink public/storage berhasil dibuat" >> /var/log/entrypoint.log

echo "Starting Laravel scheduler" >> /var/log/entrypoint.log
if ! php artisan schedule:work >> /var/log/scheduler.log 2>&1; then
    echo "Error: Scheduler failed" >> /var/log/entrypoint.log
fi &


# Start Laravel queue worker with Redis
echo "Starting Laravel queue worker" >> /var/log/entrypoint.log
if ! php artisan queue:work redis --tries=3 >> /var/log/entrypoint.log 2>&1; then
    echo "Error: Queue worker failed" >> /var/log/entrypoint.log
fi &

# Start FrankenPHP
echo "Starting FrankenPHP" >> /var/log/entrypoint.log
if ! command -v frankenphp > /dev/null; then
    echo "Error: frankenphp binary not found" >> /var/log/entrypoint.log
    exit 1
fi
frankenphp php-server --root /app/public --listen 0.0.0.0:8000 >> /var/log/entrypoint.log 2>&1

exec "$@"