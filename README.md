# TDC Marketplace - Website Chợ Đồ Học Tập Cũ Cho Sinh Viên

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-green.svg)](https://vuejs.org)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-blue.svg)](https://mysql.com)
[![Docker](https://img.shields.io/badge/Docker-Compose-blue.svg)](https://docker.com)

## 📋 Giới thiệu

**TDC Marketplace** là nền tảng kết nối giữa sinh viên và cựu sinh viên nhằm trao đổi, mua bán đồ học tập cũ trong phạm vi Trường Cao đẳng Công nghệ Thủ Đức. Dự án hướng đến việc tiết kiệm chi phí, khuyến khích tái sử dụng và lan tỏa tinh thần chia sẻ trong cộng đồng sinh viên.

### 🎯 Mục tiêu
- Xây dựng SPA Vue 3 + Laravel 10 + MySQL
- Hỗ trợ đăng tin đồ cũ, tìm kiếm nâng cao, chat/offer
- Kiểm duyệt admin, thông báo và báo cáo vi phạm
- Đối tượng: Buyer (tân SV), Seller (SV khóa trên/alumni), Admin

## 🛠️ Công nghệ sử dụng

### Backend
- **Laravel 10** - PHP Framework
- **MySQL 8.0** - Database
- **Laravel Sanctum** - API Authentication
- **Spatie Permission** - RBAC
- **Laravel Scout + Meilisearch** - Full-text Search
- **Redis** - Caching & Queue
- **Intervention Image** - Image Processing

### Frontend
- **Vue 3** - JavaScript Framework
- **Vite** - Build Tool
- **Pinia** - State Management
- **Vue Router** - Routing
- **Axios** - HTTP Client
- **TailwindCSS** - CSS Framework
- **TypeScript** - Type Safety

### DevOps
- **Docker & Docker Compose** - Containerization
- **Nginx + PHP-FPM** - Web Server

## 🚀 Cài đặt và chạy dự án

### Yêu cầu hệ thống
- Docker & Docker Compose
- Git
- Ports: 5173 (Frontend), 8001 (Backend), 3307 (MySQL), 7701 (Meilisearch), 6380 (Redis)

### 1. Clone repository
```bash
git clone <repository-url>
cd TDC-Marketplace
```

### 2. Cấu hình môi trường
```bash
# Copy file cấu hình
cp backend/.env.example backend/.env

# Chỉnh sửa file .env nếu cần
nano backend/.env
```

### 3. Chạy dự án với Docker
```bash
# Build và khởi động tất cả services
docker compose up --build

# Chạy trong background
docker compose up -d --build
```

### 4. Thiết lập database
```bash
# Chạy migrations và seeders
docker compose exec laravel php artisan migrate:fresh --seed

# Tạo storage link
docker compose exec laravel php artisan storage:link
```

### 5. Truy cập ứng dụng
- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost:8001
- **Meilisearch**: http://localhost:7701
- **MySQL**: localhost:3307

## 📁 Cấu trúc dự án

```
TDC-Marketplace/
├── backend/                 # Laravel API Backend
│   ├── app/
│   │   ├── Http/Controllers/    # API Controllers
│   │   ├── Models/             # Eloquent Models
│   │   └── Http/Requests/      # Form Validation
│   ├── database/
│   │   ├── migrations/         # Database Migrations
│   │   └── seeders/           # Database Seeders
│   ├── routes/
│   │   └── api.php            # API Routes
│   └── Dockerfile
├── frontend/                # Vue.js Frontend
│   ├── src/
│   │   ├── components/        # Vue Components
│   │   ├── views/            # Page Views
│   │   ├── services/         # API Services
│   │   └── router/           # Vue Router
│   └── Dockerfile
├── docker-compose.yml       # Docker Services
└── README.md
```

## 🗄️ Database Schema

Dự án sử dụng 20 bảng chính:

### Core Tables
- `users` - Thông tin người dùng
- `seller_profiles` - Hồ sơ người bán
- `categories` - Danh mục sản phẩm
- `listings` - Tin rao sản phẩm
- `listing_images` - Hình ảnh sản phẩm

### Feature Tables
- `wishlists` - Danh sách yêu thích
- `offers` - Đề nghị mua hàng
- `reviews` - Đánh giá người bán
- `reports` - Báo cáo vi phạm
- `disputes` - Tranh chấp
- `notifications` - Thông báo
- `orders` - Đơn hàng
- `payments` - Thanh toán

### System Tables
- `audit_logs` - Log hệ thống
- `user_activities` - Hoạt động người dùng
- `campus_pickups` - Địa điểm giao dịch
- `legal_docs` - Tài liệu pháp lý
- `user_consents` - Đồng ý người dùng

## 🔧 Các lệnh hữu ích

### Laravel Commands
```bash
# Chạy migrations
docker compose exec laravel php artisan migrate

# Tạo migration mới
docker compose exec laravel php artisan make:migration create_table_name

# Chạy seeders
docker compose exec laravel php artisan db:seed

# Clear cache
docker compose exec laravel php artisan cache:clear
docker compose exec laravel php artisan config:clear
docker compose exec laravel php artisan route:clear

# Tạo controller
docker compose exec laravel php artisan make:controller Api/ControllerName

# Tạo model
docker compose exec laravel php artisan make:model ModelName -m
```

### Docker Commands
```bash
# Xem logs
docker compose logs [service_name]

# Restart service
docker compose restart [service_name]

# Dừng tất cả services
docker compose down

# Dừng và xóa volumes
docker compose down -v
```

## 📊 API Endpoints

### Authentication
- `POST /api/register` - Đăng ký tài khoản
- `POST /api/login` - Đăng nhập
- `POST /api/logout` - Đăng xuất
- `GET /api/user` - Thông tin user hiện tại

### Listings
- `GET /api/listings` - Danh sách tin rao
- `POST /api/listings` - Tạo tin rao mới
- `GET /api/listings/{id}` - Chi tiết tin rao
- `PUT /api/listings/{id}` - Cập nhật tin rao
- `DELETE /api/listings/{id}` - Xóa tin rao

### Search
- `GET /api/search` - Tìm kiếm sản phẩm
- `GET /api/categories` - Danh mục sản phẩm

## 🧪 Testing

```bash
# Chạy tests Laravel
docker compose exec laravel php artisan test

# Chạy tests với coverage
docker compose exec laravel php artisan test --coverage
```

## 📝 Chức năng chính (40 chức năng)

### Frontend (20 chức năng)
1. Trang chủ & Trang danh mục
2. Đăng ký/Đăng nhập (form, giao diện)
3. Hồ sơ cá nhân & chỉnh sửa
4. Đăng tin rao vặt (form FE)
5. Giao diện tìm kiếm & filter nâng cao
6. Theo dõi người bán (Follow Seller)
7. Trang chi tiết tin rao
8. Lưu lịch sử tìm kiếm & gợi ý thông minh
9. Skeleton loading + Dark mode
10. Trang tin tức / thông báo nội bộ
11. API đăng ký/đăng nhập (JWT/Sanctum)
12. Phân quyền người dùng (RBAC)
13. API CRUD tin rao + duyệt tin (Admin)
14. Danh sách yêu thích
15. API thông báo (queue + email)
16. API báo cáo vi phạm
17. API khiếu nại/tranh chấp
18. API tìm kiếm (Scout/Meili)
19. Xác thực sinh viên
20. Audit log (ghi vết thao tác)

### Backend (20 chức năng)
21-40. Các chức năng backend khác...

## 🤝 Đóng góp

1. Fork dự án
2. Tạo feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Mở Pull Request

## 📄 License

Dự án này được phát triển cho mục đích học tập tại Trường Cao đẳng Công nghệ Thủ Đức.

## 👥 Nhóm phát triển

**Nhóm E - Chuyên đề Phát triển Web 1**
- **GVHD**: Phan Thanh Nhuần
- **Thành viên**: 
  - Trần Quốc Nam
  - Nguyễn Châu Phi  
  - Lê Đồng Minh Tuấn
  - Trương Tuấn Dũng

## 📞 Liên hệ

- **Email**: [phipari12345@gmail.com]
- **Địa chỉ**: Trường Cao đẳng Công nghệ Thủ Đức

---

**Lưu ý**: Dự án đang trong giai đoạn phát triển. Một số chức năng có thể chưa hoàn thiện.