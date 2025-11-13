#!/bin/sh
# entrypoint.sh — Khởi động Laravel + đồng bộ dữ liệu Elasticsearch

echo "🚀 Bắt đầu khởi động Laravel..."

# ⏳ Chờ MySQL và Elasticsearch sẵn sàng (10–15 giây)
sleep 10

# 📦 Đảm bảo vendor đã sẵn sàng (chỉ cài khi thiếu)
if [ ! -f vendor/autoload.php ]; then
  echo "📦 Vendor chưa có, đang cài đặt dependencies..."
  composer install --no-interaction --prefer-dist --optimize-autoloader || true
else
  echo "📦 Vendor đã tồn tại trong container, bỏ qua bước cài đặt."
fi

# 🔧 Dọn cache cũ
echo "🔧 Dọn dẹp cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 🔐 Fix permissions cho storage và cache
echo "🔐 Thiết lập quyền truy cập..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 🧩 Migrate & seed database (nếu chưa có)
echo "🧩 Đang migrate và seed database..."
php artisan migrate --force || true

# 🔎 Kiểm tra Elasticsearch index
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

# ⚡ Optimize cache cho production
echo "⚡ Tối ưu hóa cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 🚀 Khởi động Laravel server
echo "🌐 Laravel đang chạy tại http://localhost:8000"
php artisan serve --host=0.0.0.0 --port=8000
