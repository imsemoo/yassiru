#!/bin/bash
set -e

echo "=== يسّرو — بدء النشر ==="

# Pull latest code
echo "→ سحب آخر التحديثات..."
git pull origin main

# Build and restart containers
echo "→ بناء وتشغيل الحاويات..."
docker compose -f docker-compose.prod.yml build --no-cache
docker compose -f docker-compose.prod.yml up -d

# Wait for app to be ready
echo "→ انتظار التطبيق..."
sleep 5

# Run migrations
echo "→ تشغيل الترحيلات..."
docker exec yassiru-app php artisan migrate --force

# Clear and rebuild caches
echo "→ تحديث الكاش..."
docker exec yassiru-app php artisan config:cache
docker exec yassiru-app php artisan route:cache
docker exec yassiru-app php artisan view:cache

# Restart queue workers to pick up new code
echo "→ إعادة تشغيل Queue Worker..."
docker restart yassiru-queue

echo "=== ✓ تم النشر بنجاح ==="
echo "→ الموقع: https://yassiru.com"
