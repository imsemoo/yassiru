#!/bin/bash
# يسّرو — نسخ احتياطي لقاعدة البيانات
set -e

BACKUP_DIR="/backups/yassiru"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
mkdir -p "$BACKUP_DIR"

echo "→ بدء النسخ الاحتياطي..."
docker exec yassiru-mysql mysqldump -u yassiru -p"${DB_PASSWORD:-secret}" yassiru | gzip > "$BACKUP_DIR/yassiru_${TIMESTAMP}.sql.gz"

# Keep only last 14 days
find "$BACKUP_DIR" -name "*.sql.gz" -mtime +14 -delete

echo "✓ تم النسخ الاحتياطي: yassiru_${TIMESTAMP}.sql.gz"
echo "→ الحجم: $(du -h "$BACKUP_DIR/yassiru_${TIMESTAMP}.sql.gz" | cut -f1)"
