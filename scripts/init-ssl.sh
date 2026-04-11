#!/bin/bash
# يسّرو — إعداد شهادة SSL لأول مرة
set -e

echo "→ طلب شهادة SSL من Let's Encrypt..."
docker compose -f docker-compose.prod.yml run --rm certbot certonly \
  --webroot \
  --webroot-path=/var/www/public \
  -d yassiru.com \
  -d www.yassiru.com \
  --email islam@yassiru.com \
  --agree-tos \
  --no-eff-email

echo "→ إعادة تشغيل Nginx..."
docker restart yassiru-nginx

echo "✓ تم تثبيت شهادة SSL بنجاح"
echo "→ الموقع الآن متاح على: https://yassiru.com"
