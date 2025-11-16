# 🚀 TDC Marketplace - Quick Start Guide

## 📋 Tóm tắt dự án

**TDC Marketplace** là website chợ đồ học tập cũ cho sinh viên Trường Cao đẳng Công nghệ Thủ Đức với các tính năng:

- 🛒 **Marketplace hoàn chỉnh**: Mua bán đồ học tập cũ
- 💰 **Hệ thống Escrow**: Ký quỹ an toàn cho giao dịch  
- 🔍 **Tìm kiếm thông minh**: Meilisearch + Elasticsearch
- 🔐 **Bảo mật cao**: RBAC, audit logs, JWT authentication
- 📱 **Real-time**: Notifications, live updates
- 💳 **Thanh toán**: MoMo, VNPay, ZaloPay integration

## 🏗️ Kiến trúc công nghệ

### Backend (Laravel 10)
- **Framework**: Laravel 10 + PHP 8.3
- **Database**: MySQL 8.0
- **Authentication**: Laravel Sanctum (API tokens)
- **Cache/Queue**: Redis 7.0
- **Search**: Laravel Scout + Meilisearch + Elasticsearch
- **Packages chính**:
  - Spatie Permission (RBAC)
  - Spatie Media Library (File handling)
  - Intervention Image (Image processing)
  - Maatwebsite Excel (Export data)

### Frontend (Vue 3)
- **Framework**: Vue 3 + Vite
- **State Management**: Pinia
- **Styling**: TailwindCSS
- **Language**: TypeScript

### DevOps & Infrastructure
- **Containerization**: Docker Compose (8 services)
- **Search Engines**: Meilisearch + Elasticsearch + Solr
- **Database Admin**: phpMyAdmin
- **Development**: Hot reload, auto-restart

## 📊 Database Schema (20+ Tables)

### Core Business Tables
- `users` - Người dùng (buyer/seller/admin)
- `listings` - Tin rao vặt với kiểm duyệt
- `categories` - Danh mục sản phẩm  
- `orders` - Đơn hàng với escrow system
- `payments` - Thanh toán đa dạng

### Advanced Features
- `offers` - Thương lượng giá
- `wishlists` - Danh sách yêu thích
- `reviews` & `ratings` - Đánh giá người bán
- `reports` & `disputes` - Báo cáo và giải quyết tranh chấp
- `escrow_accounts` - Tài khoản ký quỹ an toàn

### System & Analytics  
- `audit_logs` - Ghi vết mọi hoạt động
- `user_activities` - Phân tích hành vi
- `notifications` - Thông báo real-time
- `pickup_points` - Điểm giao dịch an toàn

## ⚡ Quick Start (Đã Setup Thành Công)

### Bước 1: Kiểm tra Docker services
```bash
docker compose ps
```

### Bước 2: Truy cập ứng dụng

| Service | URL | Mô tả |
|---------|-----|--------|
| **Frontend (Vue)** | http://localhost:5174 | Giao diện chính |
| **Backend API** | http://localhost:8001 | Laravel API |
| **phpMyAdmin** | http://localhost:8080 | Quản lý database |
| **Meilisearch** | http://localhost:7701 | Search dashboard |
| **Elasticsearch** | http://localhost:9200 | Advanced search |

### Bước 3: Đăng nhập Database
- **Host**: localhost:3307  
- **Username**: laravel
- **Password**: password
- **Database**: tdc_marketplace

## 🎯 Workflow người dùng

### User Journey
1. **Đăng ký** → Xác thực email sinh viên TDC
2. **Phân quyền** → Buyer/Seller tự động
3. **Đăng tin** → Upload sản phẩm, chờ admin duyệt
4. **Tìm kiếm** → Full-text search, lọc theo danh mục
5. **Thương lượng** → Hệ thống offer/counter-offer  
6. **Đặt hàng** → Thanh toán qua escrow
7. **Giao dịch** → Tại pickup points, QR confirm
8. **Đánh giá** → Review seller sau giao dịch

### Admin Workflow  
1. **Kiểm duyệt** tin rao mới
2. **Quản lý** reports và disputes
3. **Thống kê** analytics dashboard
4. **Cấu hình** pickup points, categories

## 🔧 Development Commands

### Docker Management
```bash
# Xem logs
docker compose logs laravel
docker compose logs vue

# Restart service
docker compose restart laravel
docker compose restart vue

# Access containers
docker compose exec laravel bash
docker compose exec mysql mysql -u laravel -p

# Stop/Start all
docker compose down
docker compose up -d
```

### Laravel Commands
```bash
# Database operations
docker compose exec laravel php artisan migrate
docker compose exec laravel php artisan migrate:fresh --seed

# 🔄 Reset database và seed lại data mới
docker compose exec laravel php artisan migrate:fresh --seed

# ⚡ Chỉ seed lại data (không xóa database)
docker compose exec laravel php artisan db:seed

# 🎯 Seed specific seeder
docker compose exec laravel php artisan db:seed --class=CategorySeeder
docker compose exec laravel php artisan db:seed --class=PickupPointSeeder
docker compose exec laravel php artisan db:seed --class=ListingSeeder

# Cache operations  
docker compose exec laravel php artisan cache:clear
docker compose exec laravel php artisan config:clear
docker compose exec laravel php artisan route:clear

# Generate key
docker compose exec laravel php artisan key:generate

# Search indexing
docker compose exec laravel php artisan scout:import

# Routes testing
docker compose exec laravel php artisan route:list --path=api
```

### API Endpoints (Fixed)
```bash
# User profile (đã fix API endpoints)
GET /api/user                  # Lấy thông tin user hiện tại  
PUT /api/user                  # Cập nhật profile user

# Authentication
POST /api/auth/login           # Đăng nhập
POST /api/auth/register        # Đăng ký
POST /api/auth/logout          # Đăng xuất
GET  /api/auth/me             # Thông tin user từ token

# Core features  
GET /api/listings             # Danh sách tin rao
GET /api/categories           # Danh mục sản phẩm
GET /api/search              # Tìm kiếm với Meilisearch
GET /api/wishes              # Wishlist
```

### Frontend Development
```bash
# Access Vue container
docker compose exec vue sh

# Install packages
docker compose exec vue npm install

# Build for production
docker compose exec vue npm run build
```

## 🗄️ Database Sample Data

Database đã được khởi tạo với dữ liệu mẫu đa dạng:

### Roles & Permissions
- ✅ **Admin**: Toàn quyền quản lý (admin@tdc.edu.vn)
- ✅ **Seller**: Đăng tin, quản lý orders  
- ✅ **Buyer**: Mua hàng, đánh giá

### Sample Categories (8 loại)
- 📚 **Sách giáo khoa** - Sách giáo khoa các môn học
- 📖 **Sách tham khảo** - Tài liệu học tập, giáo trình
- ✏️ **Đồ dùng học tập** - Bút, vở, máy tính...
- 💻 **Điện tử** - Laptop, điện thoại, tai nghe...
- 👕 **Quần áo** - Quần áo, giày dép, phụ kiện
- 🪑 **Đồ nội thất** - Bàn, ghế, tủ, kệ...
- ⚽ **Thể thao** - Dụng cụ thể thao, quần áo
- 🎒 **Khác** - Balo, đèn học, đồ dùng cá nhân

### Sample Listings (27+ tin rao)
- ✅ **18 tin đã duyệt** - Đa dạng từng category, có pickup point
- ⏳ **7 tin chờ duyệt** - Trạng thái pending
- ❌ **2 tin bị từ chối** - Có rejection reason

### Campus Pickup Points (8 địa điểm)
- ✅ **Cổng chính TDC** (TDC-MAIN) - 53 Võ Văn Ngân
- ✅ **Thư viện TDC** (TDC-LIB) - Nhà E, có giờ mở cửa
- ✅ **Sảnh nhà A** (TDC-A) - Khu hành chính
- ✅ **Căng tin sinh viên** (TDC-CANTEEN) - Tầng 1 khu B
- ✅ **Khu vực sân bóng** (TDC-SPORT) - Sân thể thao
- ✅ **Phòng Đoàn - Hội** (TDC-UNION) - Tầng 2 nhà C
- ✅ **Khu đậu xe** (TDC-PARKING) - Bãi xe khu A
- ✅ **Quầy photocopy** (TDC-PHOTO) - Cạnh thư viện

**Mỗi pickup point có:**
- 📍 Tọa độ GPS (lat/lng)
- 🕐 Opening hours (JSON format)
- 🏷️ Campus code
- ✅ Active status

### Test Accounts (Password: `password`)
- **Admin**: 
  - Email: `admin@tdc.edu.vn`
  - Password: `password`
  - Role: Full admin access
  
- **Students/Users**: 
  - Email: `nguyenvana@tdc.edu.vn`
  - Email: `tranthib@tdc.edu.vn`  
  - Email: `levanc@tdc.edu.vn`
  - Password: `password` (cho tất cả accounts)
  - Role: User (có thể buy/sell)

### 🔄 Reset Database & Seed Lại Data Mới

**Cách 1: Reset toàn bộ (xóa hết, tạo lại)**
```bash
docker compose exec laravel php artisan migrate:fresh --seed
```
Lệnh này sẽ:
1. Drop tất cả tables
2. Chạy lại tất cả migrations
3. Seed toàn bộ data mới

**Cách 2: Chỉ seed lại data (giữ nguyên structure)**
```bash
docker compose exec laravel php artisan db:seed
```
⚠️ **Lưu ý:** Có thể bị duplicate key nếu data đã tồn tại

**Cách 3: Seed từng loại riêng biệt**
```bash
# Seed categories
docker compose exec laravel php artisan db:seed --class=CategorySeeder

# Seed pickup points
docker compose exec laravel php artisan db:seed --class=PickupPointSeeder

# Seed listings
docker compose exec laravel php artisan db:seed --class=ListingSeeder
```

**Sau khi seed xong, nên:**
```bash
# Clear cache
docker compose exec laravel php artisan cache:clear

# Reindex search (nếu dùng Scout)
docker compose exec laravel php artisan scout:import "App\Models\Listing"
```

## 🛡️ Security Features

### Escrow Payment System
- 💰 Tiền được giữ trong hệ thống
- 🔒 Chỉ release khi confirm giao hàng
- ⚖️ Dispute resolution nếu có tranh chấp
- 📊 Tracking đầy đủ payment flow

### Role-Based Access Control
- 🎭 Spatie Permission package  
- 🔐 API middleware protection
- 📝 Audit logs mọi thao tác
- 🚫 Rate limiting & validation

### Data Protection
- 🔑 JWT token authentication
- 🛡️ XSS & CSRF protection  
- 📧 Email verification required
- 🏫 Student domain validation

## 📈 Performance & Scaling

### Search Optimization
- **Meilisearch**: Instant search, typo tolerance
- **Elasticsearch**: Advanced analytics, aggregations
- **Redis Cache**: Session, query caching
- **Database Indexing**: Optimized for common queries

### File Handling
- 📸 **Intervention Image**: Auto resize, optimize  
- 💾 **Spatie Media Library**: Organized file storage
- 🖼️ **Multiple formats**: JPEG, PNG, WebP support
- 📱 **Responsive images**: Different sizes for devices

## 🔍 Troubleshooting

### Common Issues & Solutions

**1. Container không khởi động:**
```bash
docker compose down -v
docker compose up --build -d
```

**2. Laravel 500 error:**  
```bash
docker compose exec laravel php artisan key:generate
docker compose exec laravel php artisan config:clear
```

**3. Database connection error:**
```bash
docker compose exec laravel php artisan migrate:status
# Check .env database config
```

**4. Search không hoạt động:**
```bash
docker compose exec laravel php artisan scout:flush
docker compose exec laravel php artisan scout:import
```

**5. Frontend không load:**
```bash
docker compose logs vue
docker compose restart vue  
```

**6. API gọi sai port (ERR_CONNECTION_REFUSED):**
- ❌ Frontend call `localhost:8000` → ✅ Fix: Use `/api/*` với Vite proxy
- ✅ **Đã fix**: ProfileView.vue sử dụng `/api/user` thay vì hardcode URL
- ✅ **Đã fix**: Tất cả API calls qua Vite proxy trong Docker network

**7. 401 Unauthorized khi vào /profile:**
- ❌ **Nguyên nhân**: Sử dụng sai localStorage key (`token` vs `auth_token`)
- ✅ **Đã fix**: ProfileView.vue dùng `auth_token` khớp với auth store
- ℹ️ **Cách kiểm tra**: 
  ```javascript
  // Check trong browser console
  localStorage.getItem('auth_token') // Phải có token
  localStorage.getItem('user')       // Phải có user info
  ```
- 📌 **Để đăng nhập**: Vào http://localhost:5174/login với account test

**8. Missing route warning:**
- ✅ **Đã fix**: Thêm route `/privacy-policy` vào router

## 🎯 Next Steps - Development

### Feature Development Areas
1. **Payment Integration**: Hoàn thiện MoMo/VNPay
2. **Mobile App**: React Native/Flutter
3. **AI Features**: Smart recommendations, chatbot
4. **Analytics**: Advanced reporting dashboard
5. **Notifications**: Push notifications, SMS
6. **Social Features**: User profiles, following

### Code Organization
- **Backend**: `/backend` - Laravel API
- **Frontend**: `/frontend` - Vue SPA  
- **Database**: `/backend/database` - Migrations, seeders
- **Docker**: `docker-compose.yml` - Service definitions
- **Documentation**: `/docs` - API docs, guides

---

## 🎉 Chúc mừng!

Dự án **TDC Marketplace** đã sẵn sàng cho development với architecture hoàn chỉnh, security cao, và tính năng marketplace chuyên nghiệp! 

🚀 **Happy Coding!**

---

*Last updated: November 12, 2025*  
*Status: ✅ All services running successfully*