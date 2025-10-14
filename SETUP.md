# 🚀 TDC Marketplace - Hướng dẫn Setup

## 📋 Yêu cầu hệ thống

- Docker & Docker Compose
- Node.js 18+ (cho development)
- PHP 8.2+ (cho development)
- Composer (cho development)

## 🛠️ Cài đặt và chạy dự án

### 1. Clone repository
```bash
git clone <repository-url>
cd TDC-Marketplace
```

### 2. Cấu hình môi trường

#### Backend (.env)
```bash
cd backend
cp .env.example .env
```

Chỉnh sửa file `.env`:
```env
APP_NAME="TDC Marketplace"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=tdc_marketplace
DB_USERNAME=laravel
DB_PASSWORD=password

REDIS_HOST=redis
REDIS_PORT=6379

MEILISEARCH_HOST=http://meilisearch:7700
MEILISEARCH_KEY=masterKey123
```

#### Frontend (.env)
```bash
cd frontend
echo "VITE_API_URL=http://localhost:8000/api" > .env
```

### 3. Chạy với Docker Compose

```bash
# Từ thư mục gốc
docker-compose up -d

# Chạy migrations và seeders
docker-compose exec laravel php artisan migrate --seed

# Tạo application key
docker-compose exec laravel php artisan key:generate

# Cài đặt dependencies cho frontend
docker-compose exec vue npm install
```

### 4. Truy cập ứng dụng

- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost:8000/api
- **Admin Panel**: http://localhost:8000/admin (sau khi tạo admin user)

## 🔧 Development Setup

### Backend (Laravel)
```bash
cd backend

# Cài đặt dependencies
composer install

# Cấu hình .env
cp .env.example .env
php artisan key:generate

# Chạy migrations và seeders
php artisan migrate --seed

# Chạy server
php artisan serve
```

### Frontend (Vue 3)
```bash
cd frontend

# Cài đặt dependencies
npm install

# Chạy development server
npm run dev
```

## 📊 Database Schema

Dự án sử dụng 20 bảng chính:

### Core Tables
- `users` - Người dùng
- `seller_profiles` - Hồ sơ người bán
- `categories` - Danh mục sản phẩm
- `listings` - Tin rao
- `listing_images` - Hình ảnh sản phẩm

### Feature Tables
- `wishlists` - Danh sách yêu thích
- `offers` - Đề nghị mua
- `reviews` - Đánh giá
- `reports` - Báo cáo vi phạm
- `disputes` - Tranh chấp

### System Tables
- `notifications` - Thông báo
- `user_activities` - Hoạt động người dùng
- `listing_views` - Lượt xem
- `audit_logs` - Nhật ký hệ thống
- `legal_docs` - Tài liệu pháp lý
- `user_consents` - Đồng ý người dùng

### Order & Payment Tables
- `orders` - Đơn hàng
- `payments` - Thanh toán
- `order_confirmations` - Xác nhận đơn hàng
- `campus_pickups` - Điểm giao hàng

## 🔑 Tài khoản mặc định

Sau khi chạy seeders, bạn có thể đăng nhập với:

**Admin:**
- Email: admin@tdc.edu.vn
- Password: password

**User:**
- Email: nguyenvana@tdc.edu.vn
- Password: password

## 🚀 API Endpoints

### Authentication
- `POST /api/auth/register` - Đăng ký
- `POST /api/auth/login` - Đăng nhập
- `POST /api/auth/logout` - Đăng xuất
- `GET /api/auth/me` - Thông tin user

### Listings
- `GET /api/listings` - Danh sách tin rao
- `GET /api/listings/{id}` - Chi tiết tin rao
- `POST /api/listings` - Tạo tin rao
- `PUT /api/listings/{id}` - Cập nhật tin rao
- `DELETE /api/listings/{id}` - Xóa tin rao

### Wishlist
- `GET /api/wishlists` - Danh sách yêu thích
- `POST /api/wishlists/{listing}/toggle` - Toggle yêu thích
- `GET /api/wishlists/{listing}/check` - Kiểm tra yêu thích

### Search
- `GET /api/search` - Tìm kiếm
- `GET /api/search/suggestions` - Gợi ý tìm kiếm

### Admin
- `GET /api/admin/dashboard` - Dashboard admin
- `GET /api/admin/listings/pending` - Tin chờ duyệt
- `POST /api/admin/listings/{id}/approve` - Duyệt tin
- `POST /api/admin/listings/{id}/reject` - Từ chối tin

## 🛡️ Bảo mật

- JWT Authentication với Laravel Sanctum
- RBAC với Spatie Permission
- Rate limiting cho API
- CORS configuration
- Input validation với Form Requests

## 📱 Frontend Features

- Vue 3 + Composition API
- TypeScript support
- TailwindCSS cho styling
- Pinia cho state management
- Vue Router cho routing
- Axios cho API calls
- VeeValidate cho form validation

## 🔧 Troubleshooting

### Lỗi database connection
```bash
# Kiểm tra MySQL container
docker-compose logs mysql

# Restart services
docker-compose restart mysql laravel
```

### Lỗi permissions
```bash
# Fix Laravel permissions
docker-compose exec laravel chown -R www-data:www-data storage bootstrap/cache
docker-compose exec laravel chmod -R 775 storage bootstrap/cache
```

### Clear cache
```bash
# Laravel cache
docker-compose exec laravel php artisan cache:clear
docker-compose exec laravel php artisan config:clear
docker-compose exec laravel php artisan route:clear

# Frontend cache
docker-compose exec vue npm run build
```

## 📞 Hỗ trợ

Nếu gặp vấn đề, vui lòng tạo issue trên GitHub repository.
