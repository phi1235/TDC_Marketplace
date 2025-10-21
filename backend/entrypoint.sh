#!/bin/sh
# entrypoint.sh — Khởi động Laravel + đồng bộ dữ liệu Elasticsearch

echo "🚀 Bắt đầu khởi động Laravel..."

# ⏳ Chờ MySQL và Elasticsearch sẵn sàng (10–15 giây)
# 🔧 Dọn cache cũ
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 🧩 Migrate & seed database (nếu chưa)
echo "🧩 Đang migrate và seed database..."
php artisan migrate --force --seed || true

# 🔍 Kiểm tra Elasticsearch index
echo "🔎 Kiểm tra Elasticsearch..."
count=$(curl -s http://elasticsearch:9200/_cat/indices?v | grep listings | wc -l)

if [ "$count" -eq 0 ]; then
  echo "⚡ Elasticsearch chưa có dữ liệu, đang index..."
  php artisan es:index-listings || true
else
  echo "✅ Elasticsearch đã có dữ liệu, bỏ qua bước index."
fi

# 🔗 Liên kết storage (đề phòng lỗi ảnh)
php artisan storage:link || true

# 🚀 Khởi chạy Laravel server
echo "🌐 Laravel đang chạy tại http://localhost:8001"
php artisan serve --host=0.0.0.0 --port=8000
