TRƯỜNG CAO ĐẲNG CÔNG NGHỆ THỦ ĐỨC
KHOA CÔNG NGHỆ THÔNG TIN
CHUYÊN ĐỀ PHÁT TRIỂN WEB 1




ĐỀ TÀI: WEBSITE CHỢ ĐỒ HỌC TẬP CŨ CHO SINH VIÊN
Nhóm: E  |  GVHD: Phan Thanh Nhuần







TP. Hồ Chí Minh, tháng 10 năm 2025
Mục lục
Danh mục hình ảnh	5
Từ viết tắt	6
PHẦN 1. MỞ ĐẦU	7
I. Giới thiệu đề tài	7
II. Mục tiêu & phạm vi	7
III. Phân chia công việc	7
PHẦN 2. NỘI DUNG	11
I. Công nghệ & Kiến trúc	11
II. Lược đồ quan hệ (ERD)	11
	12
III. Định nghĩa bảng dữ liệu	13
Bảng USERS	13
Bảng SELLER_PROFILES	14
Bảng CATEGORIES	15
Bảng LISTINGS	15
Bảng LISTING_IMAGES	16
Bảng CAMPUS_PICKUPS	17
Bảng OFFERS	17
Bảng WISHLISTS	18
Bảng REVIEWS	18
Bảng REPORTS	19
Bảng DISPUTES	19
Bảng NOTIFICATIONS	20
Bảng USER_ACTIVITIES	20
Bảng LISTING_VIEWS	20
Bảng LEGAL_DOCS	21
Bảng USER_CONSENTS	21
Bảng AUDIT_LOGS	22
Bảng ORDERS	22
Bảng PAYMENTS	23
Bảng ORDER_CONFIRMATIONS	23
IV. Đặc tả chức năng chi tiết (40 mục)	24
1. Trang chủ & Trang danh mục	24
2. Đăng ký/Đăng nhập (form, giao diện)	26
3. Hồ sơ cá nhân & chỉnh sửa	28
4. Đăng tin rao vặt (form FE)	30
5. Giao diện tìm kiếm & filter nâng cao	32
6. Theo dõi người bán (Follow Seller)	33
7. Trang chi tiết tin rao	34
8. Lưu lịch sử tìm kiếm & gợi ý thông minh	35
9. Skeleton loading + Dark mode	36
10. Trang tin tức / thông báo nội bộ	36
11. API đăng ký/đăng nhập (JWT/Sanctum)	38
12. Phân quyền người dùng (RBAC)	40
13. API CRUD tin rao + duyệt tin (Admin)	41
14. Danh sách yêu thích	42
15. API thông báo (queue + email)	43
16. API báo cáo vi phạm	45
17. API khiếu nại/tranh chấp	46
18. API tìm kiếm (Scout/Meili)	47
19. Xác thực sinh viên	48
20. Audit log (ghi vết thao tác)	50
21. Đề xuất tin rao liên quan	52
22. Quản lý tin rao của tôi	54
23. Địa điểm giao dịch (pickup point)	57
24. QR xác nhận giao/nhận	59
25. Bộ lọc nâng cao cho admin	62
26. Trang thống kê cho admin	64
27. Rating & review seller	66
28. Analytics hành vi	67
29. Moderation nâng cao (từ khóa)	69
30. Gợi ý theo ngành/kỳ học	70
31. Quản lý người dùng (Admin)	72
32. Quản lý danh mục	76
33. Trang quản trị tổng quan	79
34. Upload & tối ưu ảnh (resize, webp)	81
35. Trung tâm hỗ trợ & xử lý tranh chấp (Dispute Center)	83
36. Trang FAQ & Liên hệ	85
37. Quản lý thông báo hệ thống	88
38. Export dữ liệu báo cáo (CSV/Excel)	90
39. Trang điều khoản & lưu consent	91
40. Giám sát lỗi & cảnh báo hệ thống	93
PHẦN 3. TÀI LIỆU THAM KHẢO	94


Danh mục hình ảnh
Hình 1 Lược đồ thực thể - quan hệ (ERD) của hệ thống TDC Marketplace	11
Hình 2 Giao diện trang chủ và danh mục	23
Hình 3 Giao diện form đăng ký và đăng nhập	25
Hình 4 Trang hồ sơ cá nhân và cập nhật thông tin	27
Hình 5 Form đăng tin rao vặt trên giao diện người bán	29
Hình 6 Luồng xác thực người dùng bằng JWT/Sanctum	37
Hình 7 Luồng xử lý phân quyền người dùng (RBAC)	39
Hình 8 Luồng xử lý CRUD tin rao và duyệt bài đăng	41
Hình 9 Cơ chế hàng đợi (queue) gửi thông báo và email	43
Hình 10 Quy trình xử lý báo cáo vi phạm người dùng	45
Hình 11 Luồng xử lý khiếu nại và tranh chấp giao dịch	46
Hình 12 Luồng tìm kiếm nhanh bằng MeiliSearch	47
Hình 13 Quy trình xác thực sinh viên qua email hoặc thẻ SV	49
Hình 14 Luồng ghi vết thao tác hệ thống (Audit Log)	50
Hình 15 Giao diện đề xuất tin rao liên quan	52
Hình 16 Giao diện quản lý tin rao của tôi	54
Hình 17 Giao diện địa điểm giao dịch (pickup point)	57
Hình 18 Giao diện QR xác nhận giao/nhận thanh toán	59
Hình 19 Bộ lọc nâng cao cho admin	62
Hình 20 Giao diện thống kê cho admin	64
Hình 21 Giao diện quản lý người dùng (Admin)	72
Hình 22 Giao diện quản lý danh mục	76
Hình 23 Giao diện upload & tối ưu ảnh	81
Hình 24 Giao diện trang FAQ	85
Hình 25 Giao diện trang Liên Hệ	86

Từ viết tắt
STT
Chữ viết tắt
Chữ đầy đủ
1
TDC
Trường Cao đẳng Công Nghệ Thủ Đức
2
API
Application Programming Interface
3
JWT
JSON Web Token
4
RBAC
Role-Based Access Control
5
CRUD
Create, Read, Update, Delete
6
CSV
Comma-Separated Values
7
SPA
Single Page Application
8
CI
Continuous Integration
9
ERD
Entity Relationship Diagram
10
DB
Database
11
UI
User Interface
12
UX
User Experience
13
WCAG
Web Content Accessibility Guidelines
14
PK
Primary
15
FK
Foreign Key
16
SQL
Structured Query Language
17
JSON
JavaScript Object Notation
18
SV
Sinh viên
19
DBMS
Database Management System
20
FAQ
Frequently Asked Questions

PHẦN 1. MỞ ĐẦU
I. Giới thiệu đề tài
Website TDC Marketplace là nền tảng kết nối giữa sinh viên và cựu sinh viên nhằm trao đổi, mua bán đồ học tập cũ trong phạm vi trường.
Dự án hướng đến việc tiết kiệm chi phí, khuyến khích tái sử dụng và lan tỏa tinh thần chia sẻ trong cộng đồng sinh viên.
Hệ thống được quản lý bởi Admin, đảm bảo môi trường giao dịch an toàn – minh bạch – thân thiện, góp phần tạo nên một không gian kết nối bền vững cho sinh viên TDC.
II. Mục tiêu & phạm vi
• Xây dựng SPA Vue 3 + Laravel 10 + MySQL; hỗ trợ đăng tin đồ cũ, tìm kiếm nâng cao, chat/offer, kiểm duyệt admin, thông báo và báo cáo vi phạm.
• Đối tượng: Buyer (tân SV), Seller (SV khóa trên/alumni), Admin (ban quản trị).
• Phạm vi: Chỉ tập trung chợ đồ cũ (KHÔNG triển khai bán đồ mới/giỏ hàng/đơn hàng).
III. Phân chia công việc
STT
Chức năng
Người thực hiện
Bắt đầu
Kết thúc
Trạng thái
1
Trang chủ & Trang danh mục
Trần Quốc Nam



20/09/2025
10/10/2025

2
Đăng ký/Đăng nhập (form, giao diện)




3
Trang hồ sơ cá nhân & chỉnh sửa thông tin




4
Skeleton loading + Dark mode






5
Xác thực sinh viên (email .edu/thẻ SV)




6
Địa điểm giao dịch (pickup point)




7
Quản lý danh mục




8
Trang FAQ & Liên hệ




9
Trang điều khoản & lưu consent




10
QR xác nhận giao/nhận (thanh toán)




11
Đăng tin rao vặt (form FE)
Nguyễn Châu Phi
20/09/2025
10/10/2025

12
Trang chi tiết sản phẩm/tin rao




13
API đăng ký/đăng nhập (JWT/Sanctum)




14
API CRUD tin rao + duyệt tin (Admin)




15
API báo cáo vi phạm




16
Audit log (ghi vết thao tác)




17
Quản lý tin rao của tôi




18
Analytics hành vi (lượt xem, tìm kiếm)




19
Upload & tối ưu ảnh (resize, webp)




20
Giám sát lỗi & cảnh báo hệ thống




21
Giao diện tìm kiếm & filter nâng cao
Lê Đồng Minh Tuấn
20/09/2025
10/10/2025

22
Lưu lịch sử tìm kiếm & gợi ý thông minh




23
API khiếu nại/tranh chấp




24
API tìm kiếm (Scout/Meili)




25
Đề xuất tin rao liên quan




26
Rating & review seller




27
Gợi ý theo ngành/kỳ học




28
Trung tâm hỗ trợ và xử lý tranh chấp




29
Export dữ liệu báo cáo (CSV/Excel)




30
Moderation nâng cao (lọc từ khóa)




31
Theo dõi người bán
Trương Tuấn Dũng
20/09/2025
10/10/2025

32
Trang tin tức / thông báo nội bộ




33
Phân quyền người dùng (RBAC)




34
Danh sách yêu thích




35
API thông báo (queue + email)




36
Bộ lọc nâng cao cho admin




37
Trang thống kê cho admin




38
Quản lý người dùng (khoá/mở tài khoản)




39
Trang quản trị tổng quan (dashboard)




40
Quản lý thông báo hệ thống (admin push)




PHẦN 2. NỘI DUNG
I. Công nghệ & Kiến trúc
Frontend: Vue 3 + Vite, Pinia, Vue Router, Axios, VeeValidate, TailwindCSS/Vuetify.
Backend: Laravel 10, Sanctum, Spatie Permission (RBAC), FormRequest, Queue/Mail, Storage WebP.
Database: MySQL 8; Search: Laravel Scout + Meilisearch (tùy chọn).
Triển khai: Docker/Sail, Nginx + PHP-FPM; GitHub Actions (CI).
II. Lược đồ quan hệ (ERD)
Mô hình dữ liệu tập trung cho chợ đồ cũ (KHÔNG có bảng sản phẩm/đơn hàng e-commerce).
Các bảng chính: users, seller_profiles, categories, listings, listing_image, wishlists, offers, reviews, campus_pickups, reports, disputes, notifications, legal_docs, user_activities, audit_logs, orders, payments, order_confirmations.


III. Định nghĩa bảng dữ liệu
    • Tất cả các bảng đều có thuộc tính created_at và updated_at.
Bảng USERS
STT
Thuộc tính
Kiểu dữ liệu
Ràng buộc
Mô tả
1
id
BIGINT UNSIGNED AUTO_INCREMENT
PK, NOT NULL
Khóa chính
2
name
VARCHAR(100)
NOT NULL
Tên
3
email
VARCHAR(150)
UNIQUE, NOT NULL
Email
4
password
VARCHAR(255)
NOT NULL
Mật khẩu
5
role
ENUM('user','admin')
DEFAULT 'user'
Vai trò
6
phone
VARCHAR(15)
NULL
Điện thoại
7
avatar
VARCHAR(255)
NULL
Ảnh
8
email_verified_at
TIMESTAMP
NULL
Xác thực email
9
phone_verified_at
TIMESTAMP
NULL
Xác thực số ĐT
10
is_active
BOOLEAN
DEFAULT TRUE
Trạng thái
11
last_login_at
TIMESTAMP
NULL
Đăng nhập cuối
12
login_count
INT
DEFAULT 0
Số lần đăng nhập
13
remember_token
VARCHAR(100)
NULL
Token
14
deleted_at
TIMESTAMP
NULL
Xóa mềm



Bảng SELLER_PROFILES
STT
Thuộc tính
Kiểu dữ liệu
Ràng buộc
Mô tả
1
user_id
BIGINT UNSIGNED
PK, FK -> users(id)
Người dùng
2
student_id
VARCHAR(20)
NULL
Mã SV
3
student_id_image
VARCHAR(255)
NULL
Ảnh thẻ SV
4
verified_student
BOOLEAN
DEFAULT FALSE
Đã xác thực
5
verified_at
TIMESTAMP
NULL
Thời điểm xác thực
6
rating
DECIMAL(3,2)
DEFAULT 0.00
Điểm
7
total_ratings
INT
DEFAULT 0
Lượt đánh giá
8
total_sales
INT
DEFAULT 0
Tổng bán
9
total_revenue
DECIMAL(15,2)
DEFAULT 0.00
Doanh thu
10
bio
TEXT
NULL
Mô tả
11
academic_year
VARCHAR(20)
NULL
Năm học
12
major
VARCHAR(100)
NULL
Ngành
13
is_featured
BOOLEAN
DEFAULT FALSE
Nổi bật
14
featured_until
TIMESTAMP
NULL
Hết hạn nổi bật


Bảng CATEGORIES
STT
Thuộc tính
Kiểu dữ liệu
Ràng buộc
Mô tả
1
id
BIGINT UNSIGNED AUTO_INCREMENT
PK
Khóa chính
2
name
VARCHAR(100)
NOT NULL
Tên danh mục
3
slug
VARCHAR(120)
UNIQUE, NOT NULL
Slug
4
description
TEXT
NULL
Mô tả
5
icon
VARCHAR(100)
NULL
Icon
6
parent_id
BIGINT UNSIGNED
FK -> categories(id)
Danh mục cha
7
sort_order
INT
DEFAULT 0
Thứ tự
8
is_active
BOOLEAN
DEFAULT TRUE
Trạng thái
9
meta_title
VARCHAR(200)
NULL
SEO title
10
meta_description
TEXT
NULL
SEO desc


Bảng LISTINGS
STT
Thuộc tính
Kiểu dữ liệu
Ràng buộc
Mô tả
1
id
BIGINT AUTO_INCREMENT
PK
Khóa chính
2
seller_id
BIGINT
FK -> users(id)
Người bán
3
category_id
BIGINT
FK -> categories(id)
Danh mục
4
title
VARCHAR(200)
NOT NULL
Tiêu đề
5
slug
VARCHAR(220)
UNIQUE
Slug
6
description
TEXT
NULL
Mô tả
7
condition_grade
ENUM('A','B','C','D')
NOT NULL
Tình trạng
8
price
DECIMAL(12,2)
NOT NULL
Giá bán
9
original_price
DECIMAL(12,2)
NULL
Giá gốc
10
currency
VARCHAR(3)
DEFAULT 'VND'
Tiền tệ
11
status
ENUM(...)
DEFAULT 'pending'
Trạng thái
12
featured_until
TIMESTAMP
NULL
Tin nổi bật
13
view_count
INT
DEFAULT 0
Số lượt xem
14
favorite_count
INT
DEFAULT 0
Số lượt yêu thích


Bảng LISTING_IMAGES
STT
Thuộc tính
Kiểu dữ liệu
Ràng buộc
Mô tả
1
id
BIGINT AUTO_INCREMENT
PK
Khóa chính
2
listing_id
BIGINT
FK -> listings(id)
Sản phẩm
3
original_name
VARCHAR(255)
NOT NULL
Tên file
4
file_path
VARCHAR(500)
NOT NULL
Đường dẫn
5
file_size
INT
NOT NULL
Kích thước
6
mime_type
VARCHAR(100)
NOT NULL
Loại file
7
width
INT
NULL
Chiều rộng
8
height
INT
NULL
Chiều cao
9
is_primary
BOOLEAN
DEFAULT FALSE
Ảnh chính


Bảng CAMPUS_PICKUPS
STT
Thuộc tính
Kiểu dữ liệu
Ràng buộc
Mô tả
1
id
BIGINT AUTO_INCREMENT
PK
Khóa chính
2
name
VARCHAR(150)
NOT NULL
Tên điểm
3
description
TEXT
NULL
Mô tả
4
building
VARCHAR(100)
NULL
Tòa
5
floor
VARCHAR(20)
NULL
Tầng
6
room
VARCHAR(50)
NULL
Phòng
7
landmarks
TEXT
NULL
Mốc
8
is_active
BOOLEAN
DEFAULT TRUE
Trạng thái


Bảng OFFERS
STT
Thuộc tính
Kiểu dữ liệu
Ràng buộc
Mô tả
1
id
BIGINT AUTO_INCREMENT
PK
Khóa chính
2
listing_id
BIGINT
FK -> listings(id)
Sản phẩm
3
buyer_id
BIGINT
FK -> users(id)
Người mua
4
offer_price
DECIMAL(12,2)
NOT NULL
Giá đề nghị
5
message
TEXT
NULL
Tin nhắn
6
status
ENUM(...)
DEFAULT 'pending'
Trạng thái
7
expires_at
TIMESTAMP
NOT NULL
Hết hạn


Bảng WISHLISTS
STT
Thuộc tính
Kiểu dữ liệu
Ràng buộc
Mô tả
1
id
BIGINT AUTO_INCREMENT
PK
Khóa chính
2
user_id
BIGINT
FK -> users(id)
Người dùng
3
listing_id
BIGINT
FK -> listings(id)
Sản phẩm


Bảng REVIEWS
STT
Thuộc tính
Kiểu dữ liệu
Ràng buộc
Mô tả
1
id
BIGINT AUTO_INCREMENT
PK
Khóa chính
2
reviewer_id
BIGINT
FK -> users(id)
Người đánh giá
3
reviewee_id
BIGINT
FK -> users(id)
Người được đánh giá
4
listing_id
BIGINT
FK -> listings(id)
Sản phẩm
5
rating
TINYINT
CHECK 1-5
Điểm
6
comment
TEXT
NULL
Nội dung


Bảng REPORTS
STT
Thuộc tính
Kiểu dữ liệu
Ràng buộc
Mô tả
1
id
BIGINT AUTO_INCREMENT
PK
Khóa chính
2
reporter_id
BIGINT
FK -> users(id)
Người báo cáo
3
reportable_type
VARCHAR(50)
NOT NULL
Loại đối tượng
4
reportable_id
BIGINT
NOT NULL
ID đối tượng
5
reason_category
ENUM(...)
NOT NULL
Loại lý do
6
reason
TEXT
NOT NULL
Chi tiết
7
status
ENUM(...)
DEFAULT 'pending'
Trạng thái


Bảng DISPUTES
STT
Thuộc tính
Kiểu dữ liệu
Ràng buộc
Mô tả
1
id
BIGINT AUTO_INCREMENT
PK
Khóa chính
2
listing_id
BIGINT
FK -> listings(id)
Sản phẩm
3
opener_id
BIGINT
FK -> users(id)
Người mở
4
reason
TEXT
NOT NULL
Lý do
5
status
ENUM(...)
DEFAULT 'open'
Trạng thái


Bảng NOTIFICATIONS
STT
Thuộc tính
Kiểu dữ liệu
Ràng buộc
Mô tả
1
id
BIGINT AUTO_INCREMENT
PK
Khóa chính
2
user_id
BIGINT
FK -> users(id)
Người nhận
3
type
VARCHAR(100)
NOT NULL
Loại
4
title
VARCHAR(200)
NOT NULL
Tiêu đề
5
message
TEXT
NOT NULL
Nội dung
6
read_at
TIMESTAMP
NULL
Đã đọc


Bảng USER_ACTIVITIES
STT
Thuộc tính
Kiểu dữ liệu
Ràng buộc
Mô tả
1
id
BIGINT AUTO_INCREMENT
PK
Khóa chính
2
user_id
BIGINT
FK -> users(id)
Người dùng
3
activity_type
VARCHAR(50)
NOT NULL
Loại
4
activity_data
JSON
NULL
Dữ liệu


Bảng LISTING_VIEWS
STT
Thuộc tính
Kiểu dữ liệu
Ràng buộc
Mô tả
1
id
BIGINT AUTO_INCREMENT
PK
Khóa chính
2
listing_id
BIGINT
FK -> listings(id)
Sản phẩm
3
user_id
BIGINT
FK -> users(id)
Người xem
4
ip_address
VARCHAR(45)
NOT NULL
IP


Bảng LEGAL_DOCS
STT
Thuộc tính
Kiểu dữ liệu
Ràng buộc
Mô tả
1
id
BIGINT AUTO_INCREMENT
PK
Khóa chính
2
type
ENUM(...)
NOT NULL
Loại
3
title
VARCHAR(200)
NOT NULL
Tiêu đề
4
slug
VARCHAR(220)
UNIQUE
Slug
5
content
LONGTEXT
NOT NULL
Nội dung
6
version
VARCHAR(20)
NOT NULL
Phiên bản
7
is_active
BOOLEAN
DEFAULT FALSE
Trạng thái


Bảng USER_CONSENTS
STT
Thuộc tính
Kiểu dữ liệu
Ràng buộc
Mô tả
1
id
BIGINT AUTO_INCREMENT
PK
Khóa chính
2
user_id
BIGINT
FK -> users(id)
Người dùng
3
legal_doc_id
BIGINT
FK -> legal_docs(id)
Tài liệu
4
version
VARCHAR(20)
NOT NULL
Phiên bản
5
consented_at
TIMESTAMP
DEFAULT CURRENT_TIMESTAMP
Ngày đồng ý


Bảng AUDIT_LOGS
STT
Thuộc tính
Kiểu dữ liệu
Ràng buộc
Mô tả
1
id
BIGINT AUTO_INCREMENT
PK
Khóa chính
2
user_id
BIGINT
FK -> users(id)
Người dùng
3
action
VARCHAR(100)
NOT NULL
Hành động
4
auditable_type
VARCHAR(100)
NOT NULL
Loại đối tượng
5
auditable_id
BIGINT
NOT NULL
ID đối tượng


Bảng ORDERS
STT
Thuộc tính
Kiểu dữ liệu
Ràng buộc
Mô tả
1
id
BIGINT AUTO_INCREMENT
PK
Khóa chính
2
order_number
VARCHAR(50)
UNIQUE, NOT NULL
Mã đơn
3
buyer_id
BIGINT
FK -> users(id)
Người mua
4
seller_id
BIGINT
FK -> users(id)
Người bán
5
listing_id
BIGINT
FK -> listings(id)
Sản phẩm
6
status
ENUM(...)
DEFAULT 'pending'
Trạng thái
7
total_amount
DECIMAL(12,2)
NOT NULL
Tổng tiền
8
currency
VARCHAR(3)
DEFAULT 'VND'
Tiền tệ


Bảng PAYMENTS
STT
Thuộc tính
Kiểu dữ liệu
Ràng buộc
Mô tả
1
id
BIGINT AUTO_INCREMENT
PK
Khóa chính
2
order_id
BIGINT
FK -> orders(id)
Đơn hàng
3
payment_method
ENUM(...)
NOT NULL
Phương thức
4
amount
DECIMAL(12,2)
NOT NULL
Số tiền
5
status
ENUM(...)
DEFAULT 'pending'
Trạng thái


Bảng ORDER_CONFIRMATIONS
STT
Thuộc tính
Kiểu dữ liệu
Ràng buộc
Mô tả
1
id
BIGINT AUTO_INCREMENT
PK
Khóa chính
2
order_id
BIGINT
FK -> orders(id)
Đơn hàng
3
type
ENUM(...)
NOT NULL
Loại xác nhận
4
user_id
BIGINT
FK -> users(id)
Người xác nhận
5
status
ENUM(...)
DEFAULT 'pending'
Trạng thái



IV. Đặc tả chức năng chi tiết (40 mục)
1. Trang chủ & Trang danh mục

Hình 2 Giao diện trang chủ và danh mục
Mục đích: Hiển thị danh sách tin rao theo danh mục, nổi bật, mới nhất.
Đầu vào: category, page, sort
Đầu ra: Danh sách phân trang
Luồng xử lý: Query listings theo filter → paginate → render card.
Ràng buộc & thông báo lỗi: page size giới hạn; sort whitelist
Kết quả mong đợi: Kết quả đúng filter, tốc độ ổn định.
STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Không chọn danh mục và bấm tìm kiếm
Bắt buộc chọn category_id tồn tại trong DB
Hiển thị lỗi “Vui lòng chọn danh mục cần hiển thị.”
2
Nhập từ khóa rỗng và nhấn tìm kiếm
search không được để trống khi tìm kiếm trực tiếp
Hiển thị lỗi “Vui lòng nhập từ khóa tìm kiếm.”
3
Nhập từ khóa > 100 ký tự
search VARCHAR(100)
Hiển thị lỗi “Từ khóa tìm kiếm không được vượt quá 100 ký tự.”
4
Nhập ký tự đặc biệt trong ô tìm kiếm (VD: @@@, #*%)
Regex [A-Za-z0-9\s]+
Hiển thị lỗi “Từ khóa chứa ký tự không hợp lệ.”
5
Chọn giá trị sắp xếp không có trong danh sách (sort=random)
Chỉ cho phép: newest, popular, price_asc, price_desc
Hiển thị lỗi “Tham số sắp xếp không hợp lệ.”
6
Nhập khoảng giá sai định dạng (VD: 10000_50000)
Regex /^[0-9]+-[0-9]+$/
Hiển thị lỗi “Khoảng giá nhập không đúng định dạng.”
7
Giá tối thiểu lớn hơn giá tối đa (VD: max < min)
price_min < price_max
Hiển thị lỗi “Giá tối đa phải lớn hơn giá tối thiểu.”
8
Chọn danh mục không tồn tại trong cơ sở dữ liệu
Kiểm tra exists:categories,id
Hiển thị lỗi “Danh mục không tồn tại hoặc đã bị xóa.”
9
Không có dữ liệu trong cache Redis
Redis key null
Hiển thị lỗi “Không thể tải danh mục, vui lòng thử lại.”
10
Hệ thống xử lý quá lâu khi lọc danh mục
Timeout > 2s
Hiển thị lỗi “Hệ thống đang bận, vui lòng thử lại sau.”
11
API trả về mã lỗi 500 khi load danh sách sản phẩm
Exception server
Hiển thị lỗi “Lỗi hệ thống, không thể tải dữ liệu.”
12
Trang vượt giới hạn dữ liệu (VD: page > total_pages)
page <= total_pages
Hiển thị lỗi “Không tìm thấy dữ liệu cho trang này.”
13
Người dùng sửa URL thủ công với query sai định dạng
Validate query param
Hiển thị lỗi “Đường dẫn không hợp lệ.”

2. Đăng ký/Đăng nhập (form, giao diện)

Hình 3 Giao diện form đăng ký và đăng nhập
Mục đích: Cho phép người dùng đăng ký/đăng nhập.
Đầu vào: email, password, name (register)
Đầu ra: Gọi API, nhận token/confirm UI
Luồng xử lý: Gửi form → nhận phản hồi → lưu token (localStorage) → điều hướng.
Ràng buộc & thông báo lỗi: Validate client-side; ẩn/hiện password
Kết quả mong đợi: UX rõ ràng, thông báo lỗi hợp lệ.
STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Thiếu email khi đăng ký / đăng nhập
required / format email
Vui lòng nhập địa chỉ email hợp lệ.
2
Thiếu mật khẩu (password)
required / min: 6 ký tự
Mật khẩu phải có ít nhất 6 ký tự.
3
Thiếu họ tên (name) khi đăng ký
required (register only) / max: 50 ký tự
Vui lòng nhập họ và tên.
4
Email không hợp lệ
regex pattern check
Định dạng email không hợp lệ.
5
Mật khẩu sai khi đăng nhập
backend validation
Email hoặc mật khẩu không đúng.
6
Người dùng chưa tồn tại
user not found
Tài khoản không tồn tại.
7
Email đã được đăng ký
unique constraint
Email này đã tồn tại.
8
API phản hồi lỗi (500, 400, timeout)
network / server error
Không thể kết nối máy chủ, vui lòng thử lại.
9
Token không hợp lệ hoặc hết hạn
auth token invalid
Phiên đăng nhập đã hết hạn, vui lòng đăng nhập lại.
10
Không hiển thị / xử lý lỗi UI
UX/Render fail
Có lỗi xảy ra, vui lòng tải lại trang.

3. Hồ sơ cá nhân & chỉnh sửa

Hình 4 Trang hồ sơ cá nhân và cập nhật thông tin
Mục đích: Người dùng cập nhật thông tin và avatar.
Đầu vào: name, phone, avatar
Đầu ra: Profile đã cập nhật
Luồng xử lý: Validate → upload ảnh → gọi API update → cập nhật UI.
Ràng buộc & thông báo lỗi: Ảnh ≤ 5MB; phone 10 số
Kết quả mong đợi: Hiển thị thông báo thành công.
STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Thiếu họ và tên (name)
required / max: 50 ký tự
Vui lòng nhập họ và tên.
2
Số điện thoại (phone) không hợp lệ
pattern: 10 chữ số
Số điện thoại không hợp lệ.
3
Thiếu ảnh đại diện (avatar)
optional / required nếu cập nhật ảnh
Vui lòng chọn ảnh đại diện.
4
Ảnh đại diện vượt quá dung lượng cho phép
≤ 5MB / format: jpg, png
Ảnh tải lên vượt quá dung lượng 5MB hoặc sai định dạng.
5
Lỗi khi tải ảnh lên máy chủ
upload fail / timeout
Không thể tải ảnh lên, vui lòng thử lại.
6
Lỗi khi cập nhật thông tin (API update)
HTTP status ≠ 200
Không thể cập nhật hồ sơ, vui lòng thử lại sau.
7
Thiếu quyền truy cập (token hết hạn)
authentication required
Phiên đăng nhập hết hạn, vui lòng đăng nhập lại.
8
Cập nhật thành công nhưng UI không làm mới
UI render fail
Hồ sơ đã cập nhật nhưng không hiển thị, vui lòng tải lại trang.

4. Đăng tin rao vặt (form FE)

Hình 5 Form đăng tin rao vặt trên giao diện người bán
Mục đích: Seller tạo tin rao mới.
Đầu vào: title, category, price, condition, photos[]
Đầu ra: Listing trạng thái pending
Luồng xử lý: Validate client → upload ảnh → gọi API create.
Ràng buộc & thông báo lỗi: Ảnh định dạng hợp lệ; price > 0
Kết quả mong đợi: Tin vào hàng chờ duyệt.

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Thiếu tiêu đề bài đăng (title)
required / max: 50 ký tự
Vui lòng nhập tiêu đề bài đăng (tối đa 50 ký tự).
2
Thiếu mô tả chi tiết (description)
required / max: 1500 ký tự
Vui lòng nhập mô tả chi tiết cho sản phẩm.
3
Chưa chọn danh mục (category)
required / enum hợp lệ
Vui lòng chọn danh mục đăng tin.
4
Giá sản phẩm (price) không hợp lệ
price > 0
Giá phải lớn hơn 0.
5
Ảnh sản phẩm không đúng định dạng
image format: jpg, png, jpeg / size < 5MB
Ảnh tải lên không hợp lệ (chỉ chấp nhận JPG, PNG, JPEG, dung lượng < 5MB).
6
Lỗi upload ảnh hoặc video
upload fail / timeout
Không thể tải ảnh hoặc video lên, vui lòng thử lại.
7
Thiếu thông tin người bán (seller info)
required
Vui lòng nhập thông tin người bán.
8
Lỗi gọi API tạo tin (API create)
HTTP status ≠ 200
Không thể đăng tin, vui lòng thử lại sau.
9
Lưu nháp thất bại
localStorage / draft save fail
Không thể lưu nháp bài đăng.
10
Nội dung chứa ký tự HTML hoặc script không hợp lệ
sanitize HTML
Nội dung chứa ký tự không hợp lệ, vui lòng kiểm tra lại.

5. Giao diện tìm kiếm & filter nâng cao
Mục đích: Buyer tìm tin rao theo nhiều tiêu chí.
Đầu vào: q, category, condition, price range
Đầu ra: Kết quả phân trang
Luồng xử lý: Gọi API /search với tham số; hiển thị chips filter.
Ràng buộc & thông báo lỗi: Giới hạn phạm vi giá; debounce nhập liệu
Kết quả mong đợi: Kết quả phù hợp & có highlight.

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Thiếu hoặc rỗng tham số tìm kiếm q
required
Vui lòng nhập từ khóa tìm kiếm.
2
category không hợp lệ
enum: danh mục hợp lệ
Danh mục không tồn tại.
3
condition sai giá trị
enum: [mới, cũ, đã qua sử dụng]
Tình trạng sản phẩm không hợp lệ.
4
price range vượt giới hạn cho phép
min ≥ 0, max ≤ 1,000,000,000
Khoảng giá không hợp lệ.
5
Không có kết quả tìm kiếm
result length = 0
Không tìm thấy kết quả phù hợp.
6
API /search phản hồi lỗi
HTTP status ≠ 200
Lỗi kết nối đến máy chủ tìm kiếm.
7
Lỗi debounce nhập liệu (gọi API liên tục)
debounce ≥ 300ms
Vui lòng nhập chậm lại, hệ thống đang xử lý.
8
Lỗi hiển thị chips filter
UI render fail
Không thể hiển thị bộ lọc đã chọn.
6. Theo dõi người bán (Follow Seller)
Mục đích: Chia nhỏ danh sách tin rao thành nhiều trang để tối ưu performance.
Đầu vào: page, limit, filters
Đầu ra: Danh sách tin rao + navigation controls
Luồng xử lý: Validate params + Query với OFFSET/LIMIT + Render pagination UI
Ràng buộc & thông báo lỗi: Page > 0, limit 10-50, "Trang không tồn tại"
Kết quả mong đợi: Load nhanh < 500ms, SEO-friendly URLs

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Tham số page không hợp lệ
page > 0
Trang không tồn tại.
2
Tham số limit ngoài phạm vi cho phép
limit 10–50
Giới hạn hiển thị không hợp lệ.
3
filters không đúng định dạng hoặc rỗng
optional / JSON valid
Bộ lọc tìm kiếm không hợp lệ.
4
Lỗi truy vấn dữ liệu (OFFSET/LIMIT query fail)
database / query timeout
Không thể tải danh sách tin rao.
5
Lỗi phân trang (pagination render fail)
required / valid total pages
Không thể hiển thị phân trang.
6
Thời gian phản hồi > 500ms
response time < 500ms
Trang tải chậm, vui lòng thử lại sau.
7
URL không thân thiện với SEO
slug pattern: ^[a-z0-9-]+$
Đường dẫn không đúng định dạng SEO-friendly.
7. Trang chi tiết tin rao
Mục đích: Hiển thị thông tin đầy đủ một tin.
Đầu vào: listing_id
Đầu ra: Chi tiết + ảnh + seller box
Luồng xử lý: Fetch detail + tăng view + gợi ý liên quan.
Ràng buộc & thông báo lỗi: Kiểm tra id hợp lệ
Kết quả mong đợi: Trang tải ổn định, ảnh tối ưu.

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Thiếu hoặc sai listing_id
required / integer / valid id
ID tin rao không hợp lệ.
2
Không tìm thấy tin rao
exists(listing_id)
Tin rao không tồn tại hoặc đã bị xóa.
3
Lỗi tải ảnh sản phẩm
image_url valid / optimize
Không thể hiển thị ảnh sản phẩm.
4
Lỗi tải thông tin người bán (seller box)
foreign key / join fail
Không thể tải thông tin người bán.
5
Lỗi khi tăng lượt xem
update counter fail
Không thể cập nhật lượt xem tin rao.
6
Gợi ý liên quan không hiển thị
fetch related fail
Không thể hiển thị tin liên quan.
7
Thời gian tải trang quá lâu
load time < 2s
Trang tải chậm, vui lòng thử lại sau.
8
Ảnh không được tối ưu hoặc sai kích thước
image optimize / size limit
Ảnh hiển thị chưa được tối ưu.

8. Lưu lịch sử tìm kiếm & gợi ý thông minh
Mục đích: Lưu hành vi tìm kiếm và đưa ra gợi ý cá nhân hóa.
Đầu vào: user_id, search_query, filters
Đầu ra: Lịch sử tìm kiếm + autocomplete suggestions + sản phẩm đề xuất
Luồng xử lý: Lưu search history + Phân tích pattern + Generate suggestions + Cache Redis
Ràng buộc & thông báo lỗi: Giới hạn 100 lịch sử/user, không lưu spam keywords
Kết quả mong đợi: Autocomplete < 200ms, gợi ý chính xác 80%+

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
user_id không hợp lệ hoặc không tồn tại
required / foreign key
Người dùng không hợp lệ.
2
search_query để trống
required
Vui lòng nhập từ khóa tìm kiếm.
3
search_query chứa từ khóa spam hoặc không hợp lệ
validate keyword / regex
Từ khóa tìm kiếm không hợp lệ.
4
Vượt quá giới hạn 100 lịch sử tìm kiếm / user
limit: 100 records
Vượt quá giới hạn lưu trữ lịch sử tìm kiếm.
5
Thời gian phản hồi autocomplete > 200ms
response time < 200ms
Hệ thống phản hồi chậm, vui lòng thử lại.
6
Gợi ý sai hoặc không chính xác
accuracy ≥ 80%
Kết quả gợi ý chưa đủ chính xác.
7
Lỗi cache Redis khi truy cập dữ liệu
cache read/write
Không thể truy cập dữ liệu gợi ý, vui lòng thử lại sau.

9. Skeleton loading + Dark mode
Mục đích: Tăng UX khi tải chậm, hỗ trợ dark mode.
Đầu vào: -
Đầu ra: Skeleton, theme switch
Luồng xử lý: Hiện skeleton khi pending; lưu theme trong localStorage.
Ràng buộc & thông báo lỗi: WCAG tối thiểu AA
Kết quả mong đợi: Trải nghiệm mượt, không giật.

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Skeleton không hiển thị khi tải dữ liệu
required (pending state)
Không hiển thị skeleton khi đang tải dữ liệu.
2
Theme không được lưu khi người dùng thay đổi
localStorage set/get
Không thể lưu chế độ giao diện. Vui lòng thử lại.
3
Dark mode hiển thị sai màu chữ hoặc nền
WCAG ≥ AA
Màu nền và chữ không đạt chuẩn độ tương phản.
4
Giao diện bị giật khi chuyển giữa light/dark
transition: smooth
Hiệu ứng chuyển chế độ chưa mượt.
5
Skeleton không đồng bộ với theme hiện tại
theme sync
Skeleton hiển thị sai màu so với chế độ đang chọn.

10. Trang tin tức / thông báo nội bộ
Mục đích: Kênh thông tin chính thức của Admin.
Đầu vào: title, content, thumbnail
Đầu ra: Bài viết hiển thị theo thời gian
Luồng xử lý: CRUD bài viết → hiển thị trang list + chi tiết.
Ràng buộc & thông báo lỗi: Phân trang; sanitize HTML
Kết quả mong đợi: Tin xuất bản đúng thời gian, có phân loại.

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Thiếu tiêu đề (title)
required
Vui lòng nhập tiêu đề bài viết.
2
Thiếu nội dung (content)
required
Vui lòng nhập nội dung bài viết.
3
Thiếu ảnh thumbnail
required
Vui lòng chọn ảnh thumbnail cho bài viết.
4
Nội dung chứa mã HTML không hợp lệ
sanitize HTML
Nội dung chứa ký tự hoặc mã HTML không hợp lệ.
5
Bài viết không thể lưu (lỗi cơ sở dữ liệu)
CRUD fail
Không thể lưu bài viết. Vui lòng thử lại sau.
6
Lỗi phân trang khi hiển thị danh sách
page >= 1
Không tìm thấy trang dữ liệu.
7
Lỗi sắp xếp bài viết theo thời gian
field created_at required
Không thể sắp xếp bài viết theo thời gian.
8
Thiếu hoặc sai loại tin (phân loại)
required / enum: [“Tin tức”, “Thông báo”]
Vui lòng chọn loại tin hợp lệ.

11. API đăng ký/đăng nhập (JWT/Sanctum)

Hình 6 Luồng xác thực người dùng bằng JWT/Sanctum
Mục đích: Cấp/thu hồi token đăng nhập.
Đầu vào: email, password
Đầu ra: token / message
Luồng xử lý: Validate → tạo token → trả JSON.
Ràng buộc & thông báo lỗi: Rate limit login
Kết quả mong đợi: Phản hồi lỗi rõ ràng, an toàn.



STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Thiếu email hoặc password
required
Vui lòng nhập đầy đủ email và mật khẩu.
2
Email sai định dạng
Regex: ^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$
Email không hợp lệ.
3
Mật khẩu dưới 6 ký tự
min:6
Mật khẩu quá ngắn, tối thiểu 6 ký tự.
4
Mật khẩu vượt quá 100 ký tự
max:100
Mật khẩu quá dài.
5
Tên hiển thị rỗng khi đăng ký
name required
Vui lòng nhập họ tên.
6
Email đã tồn tại
unique:users,email
Email đã được sử dụng.
7
Đăng nhập sai > 5 lần/10 phút
rate limit 5/10m 
per IP
Bạn đã đăng nhập sai quá nhiều lần, thử lại sau.
8
Tài khoản bị khóa
is_active = false
Tài khoản đã bị khóa, liên hệ quản trị viên.
9
Email chưa xác thực
email_verified_at IS NULL
Vui lòng xác thực email trước khi đăng nhập.
10
Token phiên đăng nhập không hợp lệ
JWT/Sanctum invalid/expired
Phiên đăng nhập đã hết hạn, vui lòng đăng nhập lại.

12. Phân quyền người dùng (RBAC)

Hình 7 Luồng xử lý phân quyền người dùng (RBAC)
Mục đích: Tách quyền Admin/User.
Đầu vào: role
Đầu ra: Middleware kiểm tra
Luồng xử lý: Spatie Permission → gán vai trò → policy.
Ràng buộc & thông báo lỗi: Không để lộ endpoint admin
Kết quả mong đợi: Endpoint bị chặn đúng theo vai trò.


STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Thiếu token hoặc token sai
Bắt buộc token hợp lệ (JWT/Sanctum)
Không có quyền truy cập, vui lòng đăng nhập lại.
2
Người dùng không có vai trò phù hợp
role ≠ 'admin'
Bạn không có quyền thực hiện thao tác này.
3
Cập nhật quyền cho user không tồn tại
Kiểm tra ID tồn tại
Không tìm thấy người dùng cần phân quyền.

13. API CRUD tin rao + duyệt tin (Admin)

Hình 8 Luồng xử lý CRUD tin rao và duyệt bài đăng
Mục đích: Quản trị nội dung listing.
Đầu vào: listing payload
Đầu ra: JSON listing
Luồng xử lý: Create/Update/Delete; Admin duyệt set status.
Ràng buộc & thông báo lỗi: Check quyền sở hữu; validate dữ liệu
Kết quả mong đợi: Luồng duyệt minh bạch, có lý do nếu từ chối.


STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Thiếu tiêu đề hoặc mô tả khi tạo tin
title, description bắt buộc
Vui lòng nhập tiêu đề và mô tả tin rao.
2
Ảnh không đúng định dạng
jpg, jpeg, png, max 2MB
Định dạng ảnh không hợp lệ hoặc quá dung lượng.
3
Người dùng không phải chủ bài đăng
so sánh user_id
Bạn không thể chỉnh sửa bài đăng của người khác.

14. Danh sách yêu thích
Mục đích: Cho phép user lưu và quản lý các tin rao quan tâm để xem lại sau.
Đầu vào: user_id, listing_id, action (add/remove)
Đầu ra: Danh sách tin rao đã lưu + trạng thái yêu thích + pagination
Luồng xử lý: Toggle wishlist status + Update database + Refresh UI + Sync với localStorage
Ràng buộc & thông báo lỗi: User phải đăng nhập, giới hạn 500 tin/user, "Đã thêm vào yêu thích"
Kết quả mong đợi: Response nhanh < 300ms, UI update smooth, sync cross-device

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Thêm trùng bài đã có trong yêu thích
unique(user_id, post_id)
Tin đã có trong danh sách yêu thích.
2
Xoá tin không tồn tại trong danh sách
Kiểm tra tồn tại
Tin không có trong danh sách yêu thích.

15. API thông báo (queue + email)

Hình 9 Cơ chế hàng đợi (queue) gửi thông báo và email
Mục đích: Gửi thông báo khi có sự kiện.
Đầu vào: type, payload, channels[]
Đầu ra: notification record
Luồng xử lý: Push job → gửi in-app/email → retry khi lỗi.
Ràng buộc & thông báo lỗi: Bật/tắt theo user
Kết quả mong đợi: Tỷ lệ gửi thành công cao, có read_at.


STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Không truyền kênh gửi thông báo
channel bắt buộc (email, sms,...)
Vui lòng chọn kênh gửi thông báo.
2
Job gửi thông báo thất bại
Queue retry: 3 lần
Hệ thống đang quá tải, vui lòng thử lại sau.

16. API báo cáo vi phạm

Hình 10 Quy trình xử lý báo cáo vi phạm người dùng
Mục đích: Thu thập report từ người dùng.
Đầu vào: target_type, target_id, reason
Đầu ra: report record
Luồng xử lý: Validate → tạo report → notify admin.
Ràng buộc & thông báo lỗi: Ngăn report lặp trùng
Kết quả mong đợi: Bản ghi vào hàng chờ xử lý.

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Gửi báo cáo không có nội dung
content bắt buộc
Vui lòng ghi lý do báo cáo.
2
Báo cáo chính bài viết của mình
user_id = post.user_id
Bạn không thể báo cáo bài viết của chính mình.

17. API khiếu nại/tranh chấp

Hình 11 Luồng xử lý khiếu nại và tranh chấp giao dịch
Mục đích: Xử lý tranh chấp liên quan tin.
Đầu vào: listing_id, reason
Đầu ra: dispute ticket
Luồng xử lý: Open → negotiate → resolution/close.
Ràng buộc & thông báo lỗi: SLA xử lý; audit log
Kết quả mong đợi: Ticket có kết luận, lưu lịch sử.

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Tạo khiếu nại trùng lặp
1 khiếu nại / giao dịch
Bạn đã gửi khiếu nại cho giao dịch này rồi.
2
Truy cập khiếu nại không thuộc quyền
Kiểm tra user_id liên quan
Bạn không có quyền truy cập khiếu nại này.

18. API tìm kiếm (Scout/Meili)

Hình 12 Luồng tìm kiếm nhanh bằng MeiliSearch
Mục đích: Tìm kiếm nhanh & gần đúng.
Đầu vào: q, filters
Đầu ra: Kết quả tìm
Luồng xử lý: Đồng bộ chỉ mục → query Meili.
Ràng buộc & thông báo lỗi: Fuzzy search; highlight
Kết quả mong đợi: Tốc độ < 200ms với chỉ mục nhỏ.

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Tìm kiếm rỗng không kết quả
Không có dữ liệu
Không tìm thấy kết quả phù hợp.
2
Tìm theo filter sai định dạng
filter dạng JSON hợp lệ
Tham số tìm kiếm không hợp lệ.

19. Xác thực sinh viên

Hình 13 Quy trình xác thực sinh viên qua email hoặc thẻ SV
Mục đích: Xác thực email .edu hoặc thẻ SV.
Đầu vào: method, evidence
Đầu ra: verified_student flag
Luồng xử lý: Nộp bằng chứng → admin duyệt.
Ràng buộc & thông báo lỗi: Lưu ảnh an toàn; ẩn dữ liệu nhạy cảm
Kết quả mong đợi: Cấp huy hiệu Verified Student.

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Email không phải tên miền .edu
endsWith('@...edu.vn')
Vui lòng dùng email sinh viên để xác thực.
2
Ảnh thẻ SV không rõ
dùng AI detect fail
Ảnh thẻ sinh viên không rõ ràng, vui lòng chụp lại.

20. Audit log (ghi vết thao tác)

Hình 14 Luồng ghi vết thao tác hệ thống (Audit Log)
Mục đích: Theo dõi ai làm gì.
Đầu vào: actor_id, action, entity
Đầu ra: log record
Luồng xử lý: Ghi log sau hành động quan trọng.
Ràng buộc & thông báo lỗi: Không log dữ liệu nhạy cảm
Kết quả mong đợi: Truy vết được thao tác quan trọng.


STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Không lưu log hành vi
middleware thiếu ghi log
Hệ thống không ghi nhận thao tác vừa thực hiện.
2
Lưu log thiếu thông tin
thiếu user_id, IP, action
Log không hợp lệ, vui lòng kiểm tra lại cấu hình.
21. Đề xuất tin rao liên quan

Hình 15 Giao diện đề xuất tin rao liên quan
Mục đích: Gợi ý dựa trên danh mục/từ khóa.
Đầu vào: listing_id
Đầu ra: Danh sách đề xuất
Luồng xử lý: Truy vấn theo category/keyword tương tự.
Ràng buộc & thông báo lỗi: Giới hạn số lượng
Kết quả mong đợi: Độ liên quan hợp lý.

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Không có danh mục hoặc tag đầu vào để gợi ý
Bắt buộc có category_id hoặc tags trong request
“Không thể tạo đề xuất vì thiếu danh mục hoặc tag liên quan.”
2
Danh mục không tồn tại trong cơ sở dữ liệu
exists:categories,id
“Danh mục không tồn tại hoặc đã bị xoá.”
3
Tag chứa ký tự đặc biệt (VD: #@!)
Regex ^[a-zA-Z0-9, ]+$
“Từ khóa chứa ký tự không hợp lệ.”
4
Từ khóa tìm kiếm đề xuất quá dài
max:100
“Từ khóa gợi ý vượt quá 100 ký tự.”
5
Không tìm thấy tin liên quan nào để gợi ý
Kết quả query = 0
“Không có tin rao nào tương tự.”
6
API gợi ý trả về lỗi 500
Lỗi phản hồi từ API
“Không thể tải danh sách đề xuất, vui lòng thử lại.”
7
Timeout khi truy vấn dữ liệu gợi ý > 3 giây
Thời gian xử lý vượt giới hạn
“Kết nối đến máy chủ gợi ý quá hạn.”
8
Người dùng chưa đăng nhập nhưng cố gợi ý theo cá nhân hoá
auth()->check() = false
“Vui lòng đăng nhập để xem đề xuất phù hợp.”
9
Dữ liệu trả về chứa ID tin không hợp lệ
exists:listings,id
“Một số tin được gợi ý không còn tồn tại.”
10
Bộ nhớ cache đề xuất bị lỗi khi ghi
Redis exception
“Không thể lưu bộ nhớ tạm của đề xuất.”
22. Quản lý tin rao của tôi

Hình 16 Giao diện quản lý tin rao của tôi
Mục đích: Seller xem/sửa/ẩn/xóa tin.
Đầu vào: -
Đầu ra: Danh sách tin của tôi
Luồng xử lý: Query theo seller_id + actions.
Ràng buộc & thông báo lỗi: Chỉ chủ sở hữu được sửa/xóa
Kết quả mong đợi: UI rõ ràng trạng thái tin.

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Người dùng cố gắng truy cập trang “Tin của tôi” khi chưa đăng nhập
auth()->check() = false
“Vui lòng đăng nhập để xem tin của bạn.”
2
Để trống tiêu đề khi chỉnh sửa tin
`required
string
3
Tiêu đề vượt quá 150 ký tự
max:150
“Tiêu đề tin không được vượt quá 150 ký tự.”
4
Chọn danh mục không tồn tại trong hệ thống
exists:categories,id
“Danh mục không tồn tại.”
5
Nhập giá trị âm hoặc bằng 0 khi cập nhật giá
`numeric
min:1000`
6
Nhập mô tả quá ngắn (< 20 ký tự)
min:20
“Phần mô tả quá ngắn, vui lòng viết chi tiết hơn.”
7
Tải lên hơn 5 ảnh cho 1 tin
max:5
“Bạn chỉ được tải lên tối đa 5 hình ảnh.”
8
Ảnh vượt kích thước 10MB
max:10240KB
“Ảnh có dung lượng vượt quá 10MB.”
9
Người khác cố gắng chỉnh sửa tin không thuộc sở hữu của mình
where('seller_id', auth()->id())
“Bạn không có quyền chỉnh sửa tin này.”
10
Cập nhật trạng thái không hợp lệ (ví dụ: sold_out_random)
Enum {pending,active,sold_out,deleted}
“Trạng thái cập nhật không hợp lệ.”
11
Tin đã bị xoá nhưng người dùng vẫn thao tác “Sửa”
Soft Delete flag deleted_at != null
“Tin này đã bị xoá khỏi hệ thống.”
12
API cập nhật lỗi kết nối cơ sở dữ liệu
DB::connection fail
“Không thể cập nhật tin, vui lòng thử lại.”
13
Tin đang chờ duyệt nhưng người dùng cố kích hoạt lại
status=pending
“Tin của bạn đang chờ duyệt, không thể chỉnh sửa.”

23. Địa điểm giao dịch (pickup point)

Hình 17 Giao diện địa điểm giao dịch (pickup point)
Mục đích: Chọn điểm hẹn trong campus.
Đầu vào: campus_pickup_id
Đầu ra: Cập nhật tin
Luồng xử lý: Validate id → lưu vào listing.
Ràng buộc & thông báo lỗi: Điểm hợp lệ; giờ mở cửa
Kết quả mong đợi: Hiển thị rõ ở chi tiết tin.

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Không nhập tên địa điểm khi thêm mới
`required
string
2
Nhập tên địa điểm trùng với địa điểm khác đã có
unique:campus_pickups,name
“Tên địa điểm này đã tồn tại trong hệ thống.”
3
Nhập mô tả quá 255 ký tự
max:255
“Mô tả địa điểm không được vượt quá 255 ký tự.”
4
Không chọn khuôn viên hoặc trường trực thuộc
`required
exists:campuses,id`
5
Nhập sai định dạng kinh độ/vĩ độ (ví dụ: 12.abc,-xyz)
Regex ^-?\d{1,3}\.\d+$
“Tọa độ không hợp lệ, vui lòng nhập đúng định dạng.”
6
Không nhập kinh độ hoặc vĩ độ
required
“Vui lòng nhập đủ kinh độ và vĩ độ.”
7
Người dùng thường (role=user) cố gắng thêm mới địa điểm
auth()->user()->role != 'admin'
“Chỉ quản trị viên mới có quyền thêm địa điểm.”
8
Cố gắng xóa địa điểm đang được sử dụng trong tin rao
exists:listings,campus_pickup_id
“Không thể xóa địa điểm đang được sử dụng.”
9
Server lỗi khi ghi vào cơ sở dữ liệu
DB transaction fail
“Không thể lưu địa điểm, vui lòng thử lại sau.”
10
Hệ thống mất kết nối mạng khi tải danh sách địa điểm
API timeout
“Không thể tải danh sách địa điểm. Kiểm tra kết nối mạng.”
11
Dữ liệu toạ độ null trong cơ sở dữ liệu
Column latitude hoặc longitude = null
“Địa điểm bị thiếu dữ liệu tọa độ, vui lòng cập nhật lại.”
12
Người dùng gửi request sai định dạng JSON
Content-Type != application/json
“Định dạng yêu cầu không hợp lệ.”

24. QR xác nhận giao/nhận

Mục đích: Xác nhận bàn giao trực tiếp.
Đầu vào: order_token/listing_token
Đầu ra: xác nhận thành công
Luồng xử lý: Sinh QR → quét tại điểm gặp → cập nhật trạng thái.
Ràng buộc & thông báo lỗi: Mã 1 lần; log thiết bị
Kết quả mong đợi: Lưu dấu vết thời điểm xác nhận.

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Không quét được mã QR (camera bị lỗi hoặc QR không nhận dạng)
`required
string`
2
Mã QR không tồn tại trong cơ sở dữ liệu
exists:transactions,qr_code
“Mã QR không tồn tại hoặc đã bị xoá.”
3
Mã QR đã được sử dụng trước đó
used_at != null
“Mã QR này đã được xác nhận trước đó.”
4
Người quét không phải là người mua hoặc người bán hợp lệ
auth()->id != transaction.buyer_id && seller_id
“Bạn không có quyền xác nhận giao dịch này.”
5
Giao dịch đã hoàn tất nhưng vẫn cố xác nhận lại
status=completed
“Giao dịch này đã được hoàn tất.”
6
Mã QR đã hết hạn sử dụng (> 24h kể từ khi tạo)
expires_at < now()
“Mã xác nhận đã hết hạn, vui lòng yêu cầu mã mới.”
7
Giao dịch liên kết với mã QR không tồn tại
exists:transactions,id
“Không tìm thấy giao dịch tương ứng với mã QR.”
8
Server mất kết nối khi gọi API xác nhận
Timeout > 3s
“Không thể kết nối với máy chủ xác nhận, vui lòng thử lại.”
9
Người dùng thử xác nhận offline (không có mạng)
Network error
“Không thể xác nhận khi không có kết nối mạng.”
10
Dữ liệu QR sai định dạng JSON (VD: {id: 1234} thay vì {"qr_code": "..."})
json_decode() fail
“Dữ liệu mã QR không đúng định dạng.”
11
Người mua huỷ giao dịch trước khi xác nhận
transaction.status = canceled
“Giao dịch đã bị huỷ, không thể xác nhận.”
12
Cơ sở dữ liệu không ghi được thời gian xác nhận
DB transaction fail
“Xác nhận thất bại, vui lòng thử lại sau.”
13
Người bán dùng QR của giao dịch khác
QR mismatched transaction_id
“Mã QR không khớp với giao dịch của bạn.”


25. Bộ lọc nâng cao cho admin

Hình 19 Bộ lọc nâng cao cho admin
Mục đích: Admin lọc nhanh theo trạng thái.
Đầu vào: status, reporter, verified
Đầu ra: Danh sách lọc
Luồng xử lý: Query đa điều kiện cho bảng quản trị.
Ràng buộc & thông báo lỗi: Index DB phù hợp
Kết quả mong đợi: Truy xuất nhanh, < 1s.
STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Không chọn tiêu chí lọc nào nhưng vẫn bấm “Tìm kiếm”
required
“Vui lòng chọn ít nhất một tiêu chí để lọc dữ liệu.”
2
Để trống giá trị lọc
`required
string`
3
Nhập ngày sai định dạng (VD: 12-31-2025 thay vì 2025-12-31)
Format YYYY-MM-DD
“Định dạng ngày không hợp lệ.”
4
Chọn khoảng ngày ngược (Ngày bắt đầu > Ngày kết thúc)
So sánh giá trị from > to
“Ngày bắt đầu không được lớn hơn ngày kết thúc.”
5
Nhập giá trị số âm trong bộ lọc (VD: giá < 0)
`numeric
min:0`
6
Lọc với vai trò người dùng không tồn tại
exists:roles,name
“Vai trò người dùng không hợp lệ.”
7
Lọc vượt giới hạn 1.000 bản ghi
Query limit
“Kết quả lọc quá lớn, vui lòng thu hẹp điều kiện tìm kiếm.”
8
Không có quyền truy cập tính năng lọc
auth()->user()->role != 'admin'
“Bạn không có quyền thực hiện thao tác này.”
9
Hệ thống lỗi khi gọi API thống kê
Response code 500
“Lỗi máy chủ khi lọc dữ liệu.”
10
Bộ lọc trả về kết quả rỗng
count(result)=0
“Không tìm thấy dữ liệu phù hợp.”
11
Nhập ký tự đặc biệt trong ô lọc (VD: #@!%)
Regex [A-Za-z0-9 ]+
“Giá trị lọc chứa ký tự không hợp lệ.”
12
Server timeout khi lọc quá phức tạp (> 5s)
Timeout
“Hệ thống đang xử lý chậm, vui lòng thử lại sau.”

26. Trang thống kê cho admin

Hình 20 Giao diện thống kê cho admin
Mục đích: Số tin đăng, tin chờ duyệt, report.
Đầu vào: -
Đầu ra: Cards/Charts
Luồng xử lý: Tổng hợp dữ liệu theo ngày/tuần.
Ràng buộc & thông báo lỗi: Cache số liệu
Kết quả mong đợi: Biểu đồ rõ ràng, dễ hiểu.

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Người dùng không phải admin truy cập trang thống kê
Role check auth()->user()->role != 'admin'
“Bạn không được phép truy cập trang thống kê.”
2
Không chọn khoảng thời gian thống kê
`required
date_range`
3
Chọn khoảng thời gian quá dài (> 1 năm)
max_range:365
“Khoảng thời gian thống kê vượt quá giới hạn cho phép.”
4
Không có dữ liệu trong khoảng ngày chọn
Query count() = 0
“Không có dữ liệu thống kê trong khoảng thời gian này.”
5
Cơ sở dữ liệu lỗi kết nối
DB::connection fail
“Không thể kết nối tới cơ sở dữ liệu, vui lòng thử lại.”
6
Lỗi cache Redis khi lấy dữ liệu biểu đồ
Redis exception
“Không thể tải dữ liệu thống kê từ bộ nhớ đệm.”
7
API thống kê trả về lỗi 500
Response status = 500
“Lỗi máy chủ khi tải biểu đồ thống kê.”
8
Tham số biểu đồ không hợp lệ (VD: chart_type=random)
Enum {bar, line, pie}
“Loại biểu đồ không hợp lệ.”
9
Người dùng nhập ngày sai định dạng
Format YYYY-MM-DD
“Định dạng ngày không hợp lệ.”
10
Không có quyền xem dữ liệu thống kê người khác
auth()->id != target_admin_id
“Bạn không thể xem thống kê của quản trị viên khác.”
11
Biểu đồ hiển thị trống do thiếu dữ liệu
Dataset = null
“Không có dữ liệu để hiển thị biểu đồ.”
12
Server timeout khi tổng hợp dữ liệu > 5s
Query timeout
“Hệ thống đang xử lý chậm, vui lòng thử lại sau.”

27. Rating & review seller
Mục đích: Mua đánh giá người bán.
Đầu vào: target_id, rating, comment
Đầu ra: review record
Luồng xử lý: Validate → lưu review → cập nhật điểm.
Ràng buộc & thông báo lỗi: 1 người/1 listing 1 review
Kết quả mong đợi: Điểm trung bình hiển thị công khai.

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Người mua chưa hoàn tất giao dịch nhưng cố gắng đánh giá
transaction.status != 'completed'
“Bạn chỉ có thể đánh giá sau khi giao dịch hoàn tất.”
2
Người bán tự đánh giá chính mình
seller_id == auth()->id()
“Không thể tự đánh giá bản thân.”
3
Gửi nhiều đánh giá cho cùng 1 giao dịch
unique:reviews,transaction_id
“Bạn đã đánh giá giao dịch này rồi.”
4
Không chọn số sao đánh giá
`required
integer
5
Điểm đánh giá nhỏ hơn 1 hoặc lớn hơn 5
Range 1–5
“Giá trị đánh giá không hợp lệ.”
6
Nội dung bình luận để trống
`required
min:10`
7
Bình luận quá ngắn (< 10 ký tự)
min:10
“Nội dung đánh giá quá ngắn.”
8
Bình luận vượt quá 255 ký tự
max:255
“Nội dung đánh giá quá dài (tối đa 255 ký tự).”
9
Người dùng chưa mua sản phẩm nhưng cố gắng đánh giá
exists:transactions,buyer_id
“Bạn không có quyền đánh giá người bán này.”
10
Server không lưu được đánh giá
DB transaction fail
“Không thể gửi đánh giá, vui lòng thử lại.”

28. Analytics hành vi
Mục đích: Theo dõi view/search/click.
Đầu vào: event name, props
Đầu ra: events record
Luồng xử lý: Batch insert → bảng sự kiện.
Ràng buộc & thông báo lỗi: Không ghi thông tin nhạy cảm
Kết quả mong đợi: Dashboard đơn giản thể hiện DAU/trend.

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Cookie hoặc session trống
required
“Không tìm thấy dữ liệu hành vi.”
2
Dữ liệu tracking gửi lên không hợp lệ
json_decode() fail
“Dữ liệu gửi lên không đúng định dạng JSON.”
3
Sự kiện (event_type) nằm ngoài danh sách cho phép
Enum {click, view, search, login, logout}
“Loại sự kiện không hợp lệ.”
4
Gửi dữ liệu theo dõi quá nhanh liên tục (< 1s / request)
Rate limit
“Hệ thống phát hiện hành vi gửi dữ liệu bất thường.”
5
Thiếu user_id trong dữ liệu tracking
`nullable
exists:users,id`
6
Dữ liệu tracking vượt 500 bản ghi / lần
Limit 500
“Số lượng bản ghi vượt quá giới hạn cho phép.”
7
Không kết nối được tới Redis khi lưu hành vi
Redis exception
“Không thể lưu dữ liệu hành vi, vui lòng thử lại.”
8
Mất kết nối mạng khi ghi log hành vi
Network error
“Không thể gửi dữ liệu hành vi do mất kết nối.”
9
Trình duyệt chặn cookie hoặc localStorage
Storage blocked
“Không thể thu thập dữ liệu hành vi do trình duyệt bị hạn chế.”
10
API phân tích lỗi nội bộ
HTTP 500
“Hệ thống phân tích hành vi đang gặp sự cố.”

29. Moderation nâng cao (từ khóa)
Mục đích: Tự động gắn cờ nội dung xấu.
Đầu vào: title/description
Đầu ra: flag record
Luồng xử lý: So khớp danh sách từ khóa → auto-flag.
Ràng buộc & thông báo lỗi: Giảm false-positive
Kết quả mong đợi: Admin có thể gỡ cờ/thêm whitelist.

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Không nhập từ khóa cần lọc
`required
string`
2
Từ khóa chứa ký tự đặc biệt (VD: #@!)
Regex ^[a-zA-Z0-9\s]+$
“Từ khóa chứa ký tự không hợp lệ.”
3
Nhập trùng từ khóa đã tồn tại
unique:bad_words,word
“Từ khóa đã tồn tại trong danh sách.”
4
Nhập danh sách import chứa từ khóa trùng lặp
Excel validation
“Tệp import có chứa từ khóa trùng lặp.”
5
Nhập danh sách vượt 500 dòng
Batch size limit
“Không thể nhập quá 500 từ khóa một lần.”
6
Xóa nhầm từ khóa mặc định của hệ thống
Protected flag
“Không thể xóa từ khóa mặc định.”
7
Người không có quyền kiểm duyệt cố gắng thêm từ khóa
Role check user != admin/moderator
“Bạn không có quyền thêm từ khóa mới.”
8
File import sai định dạng (VD: .txt thay vì .csv)
File type
“Định dạng tệp không hợp lệ. Vui lòng sử dụng CSV.”
9
Server không đọc được tệp upload
File open fail
“Không thể đọc tệp import. Thử lại sau.”
10
Lỗi khi ghi log hành động kiểm duyệt
DB::insert fail
“Không thể ghi log hành động kiểm duyệt.”

30. Gợi ý theo ngành/kỳ học
Mục đích: Danh sách gợi ý cho tân SV.
Đầu vào: major, semester
Đầu ra: Danh sách gợi ý
Luồng xử lý: Map ngành/kỳ → nhóm đồ thường cần.
Ràng buộc & thông báo lỗi: Cấu hình file JSON
Kết quả mong đợi: Gợi ý đúng bối cảnh học tập.

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Không chọn ngành học khi thực hiện tìm kiếm
`required
exists:majors,id`
2
Không chọn kỳ học tương ứng
`required
exists:semesters,id`
3
Người dùng chọn ngành học không tồn tại
exists:majors,id
“Ngành học được chọn không tồn tại.”
4
Người dùng chọn kỳ học không tồn tại
exists:semesters,id
“Kỳ học không tồn tại trong cơ sở dữ liệu.”
5
API gợi ý không trả về dữ liệu (trống)
Query result = 0
“Không tìm thấy kết quả phù hợp với ngành/kỳ học bạn chọn.”
6
Hệ thống lỗi khi truy vấn dữ liệu gợi ý
DB::connection fail
“Lỗi kết nối cơ sở dữ liệu, vui lòng thử lại sau.”
7
Timeout khi gọi API gợi ý (> 3 giây)
API timeout
“Máy chủ phản hồi chậm, vui lòng thử lại.”
8
Mô hình AI gợi ý không hoạt động hoặc trả về lỗi 500
Response 500
“Hệ thống gợi ý đang được bảo trì, vui lòng quay lại sau.”
9
Người dùng chưa đăng nhập nhưng chọn chế độ gợi ý cá nhân hoá
auth()->check() = false
“Vui lòng đăng nhập để sử dụng gợi ý cá nhân hoá.”
10
Dữ liệu ngành hoặc kỳ học bị null trong bảng liên kết
nullable:false
“Thiếu dữ liệu ngành/kỳ học, vui lòng cập nhật lại hồ sơ.”
11
Người dùng nhập ký tự đặc biệt trong tên ngành học (VD: CNTT@!)
Regex ^[A-Za-zÀ-ỹ\s]+$
“Tên ngành học chứa ký tự không hợp lệ.”
12
Lỗi bộ nhớ cache Redis khi lưu kết quả gợi ý
Redis exception
“Không thể lưu dữ liệu gợi ý vào bộ nhớ đệm.”
13
Dữ liệu gợi ý cũ hơn 7 ngày chưa được cập nhật
Cache TTL 7d
“Kết quả gợi ý đã hết hạn, hệ thống sẽ tải lại dữ liệu mới.”

31. Quản lý người dùng (Admin)

Hình 21 Giao diện quản lý người dùng (Admin)

Mục đích: Khoá/mở tài khoản, xem hồ sơ.
Đầu vào: user_id, action
Đầu ra: Cập nhật trạng thái
Luồng xử lý: Admin hành động → ghi audit log.
Ràng buộc & thông báo lỗi: Chỉ admin; xác nhận hai bước
Kết quả mong đợi: Trạng thái cập nhật đúng.
STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Không chọn quyền (role) khi thêm/sửa
Bắt buộc chọn user / admin
“Vui lòng chọn quyền người dùng.”
2
Khi sửa: nhập email trùng của người khác
Kiểm tra unique email ngoại trừ bản ghi hiện tại
“Email này đã được sử dụng.”
3
Khi sửa: bỏ trống trường bắt buộc
Không được để trống bất kỳ trường bắt buộc nào
“Vui lòng điền đầy đủ thông tin.”
4
Khi sửa: nhập ký tự đặc biệt trong tên hoặc địa chỉ
Không chứa ký tự đặc biệt
“Thông tin nhập không hợp lệ.”
5
Khi xoá: không chọn người dùng nào
Phải chọn ít nhất 1 bản ghi
“Vui lòng chọn người dùng cần xoá.”
6
Khi xoá: xoá chính tài khoản đang đăng nhập
Không cho phép xoá chính mình
“Không thể xoá tài khoản hiện tại.”
7
Khi xoá: user không tồn tại hoặc đã bị xoá
Kiểm tra tồn tại trước khi xoá
“Người dùng không tồn tại hoặc đã bị xoá.”
8
Khi xoá: lỗi server / API thất bại
Server trả lỗi hoặc timeout
“Không thể xoá người dùng, vui lòng thử lại.”
9
Khi nhấn khoá/mở tài khoản chính mình
Không cho phép thao tác với tài khoản đang đăng nhập
“Không thể thay đổi trạng thái tài khoản của chính bạn.”
10
Khi nhấn khoá user đã bị khoá
status == locked
“Tài khoản này đã bị khoá.”
11
Khi nhấn mở khoá user đang hoạt động
status == active
“Tài khoản này đang hoạt động.”
12
Nhập từ khoá rỗng khi tìm kiếm
search không được để trống
“Vui lòng nhập từ khoá tìm kiếm.”
13
Nhập từ khoá có ký tự đặc biệt
Chỉ chấp nhận chữ, số, dấu cách
“Từ khoá không được chứa ký tự đặc biệt.”
14
Nhập từ khoá quá dài
search VARCHAR(100)
“Từ khoá không được vượt quá 100 ký tự.”
15
Không chọn danh mục/tình trạng khi lọc
Bắt buộc chọn giá trị hợp lệ trong DB
“Vui lòng chọn danh mục hợp lệ.”
16
Nhấn phân trang đến số trang không tồn tại
page vượt tổng trang
“Trang không tồn tại.”
17
Nhập thủ công giá trị page âm hoặc không phải số
page >= 1 và kiểu số
“Giá trị trang không hợp lệ.”
18
Dữ liệu trống nhưng vẫn hiển thị phân trang
Tổng user = 0
“Không có dữ liệu để hiển thị.”
19
Lỗi tải dữ liệu khi chuyển trang
API /api/users?page=x lỗi hoặc timeout
“Không thể tải dữ liệu, vui lòng thử lại.”

32. Quản lý danh mục

Hình 22 Giao diện quản lý danh mục
Mục đích: CRUD category.
Đầu vào: name, slug
Đầu ra: category record
Luồng xử lý: Validate → lưu → cập nhật FE.
Ràng buộc & thông báo lỗi: slug unique
Kết quả mong đợi: Danh mục hiển thị ngay trên FE.

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Không nhập tên sản phẩm khi thêm mới
tensanpham bắt buộc, không được rỗng
Hiển thị lỗi “Vui lòng nhập tên sản phẩm.”
2
Tên sản phẩm quá ngắn hoặc quá dài
Chiều dài 3–100 ký tự
Hiển thị lỗi “Tên sản phẩm phải từ 3 đến 100 ký tự.”
3
Tên sản phẩm chứa ký tự đặc biệt
Không chứa ký tự đặc biệt (@, #, %, $, …)
Hiển thị lỗi “Tên sản phẩm không được chứa ký tự đặc biệt.”
4
Nhập sai định dạng số lượng
soluong chỉ được nhập số nguyên >= 0
Hiển thị lỗi “Số lượng phải là số nguyên không âm.”
5
Nhập sai định dạng giá
gia chỉ được nhập số > 0
Hiển thị lỗi “Giá sản phẩm phải lớn hơn 0.”
6
Giá vượt quá giới hạn cho phép
gia <= 10,000,000
Hiển thị lỗi “Giá sản phẩm không được vượt quá 10,000,000đ.”
7
Không nhập ngày đăng
ngaydang bắt buộc
Hiển thị lỗi “Vui lòng chọn ngày đăng sản phẩm.”
8
Nhập ngày đăng sai định dạng
Định dạng dd/mm/yyyy
Hiển thị lỗi “Ngày đăng không hợp lệ.”
9
Ghi chú quá dài
ghichu tối đa 255 ký tự
Hiển thị lỗi “Ghi chú không được vượt quá 255 ký tự.”
10
Nhấn “Thêm mới” nhưng thiếu dữ liệu bắt buộc
Các trường bắt buộc: tên, giá, ngày đăng
Hiển thị lỗi “Vui lòng nhập đầy đủ thông tin sản phẩm.”
11
Thêm sản phẩm trùng ID
ID phải là duy nhất
Hiển thị lỗi “Mã sản phẩm đã tồn tại.”
12
Sửa sản phẩm nhưng không có thay đổi
Không gửi dữ liệu khác so với bản gốc
Hiển thị lỗi “Không có thay đổi để cập nhật.”
13
Xóa sản phẩm không tồn tại
Kiểm tra ID tồn tại trong DB
Hiển thị lỗi “Sản phẩm không tồn tại hoặc đã bị xóa.”
14
Không chọn sản phẩm nhưng nhấn “Xóa”
Chưa chọn checkbox / ID
Hiển thị lỗi “Vui lòng chọn sản phẩm cần xóa.”
15
Nhập từ khóa rỗng và nhấn tìm kiếm
search không được để trống
Hiển thị lỗi “Vui lòng nhập từ khóa tìm kiếm.”
16
Nhập từ khóa có ký tự đặc biệt
search chỉ chấp nhận chữ, số, dấu cách
Hiển thị lỗi “Từ khóa tìm kiếm không được chứa ký tự đặc biệt.”
17
Nhập từ khóa quá dài
search VARCHAR(100)
Hiển thị lỗi “Từ khóa tìm kiếm không được vượt quá 100 ký tự.”
18
Không chọn dữ liệu trong bộ lọc
filter bắt buộc có giá trị hợp lệ (Bán chạy, Giảm giá, …)
Hiển thị lỗi “Vui lòng chọn kiểu lọc hợp lệ.”
19
Lọc dữ liệu không có kết quả
Không có bản ghi thỏa điều kiện
Hiển thị lỗi “Không tìm thấy sản phẩm phù hợp.”
20
Lỗi khi gọi API danh sách sản phẩm
Lỗi kết nối hoặc server
Hiển thị lỗi “Không thể tải danh sách sản phẩm, vui lòng thử lại.”
21
Nhấn vào số trang không tồn tại
page vượt quá tổng số trang
Hiển thị lỗi “Trang không tồn tại.”
22
Nhập page không hợp lệ (âm / chữ)
page >= 1, kiểu dữ liệu số
Hiển thị lỗi “Giá trị trang không hợp lệ.”
23
Không có dữ liệu nhưng vẫn hiển thị phân trang
Tổng sản phẩm = 0
Hiển thị lỗi “Không có dữ liệu để hiển thị.”
24
Lỗi tải dữ liệu khi chuyển trang
API /api/products?page=x lỗi hoặc timeout
Hiển thị lỗi “Không thể tải dữ liệu trang, vui lòng thử lại.”


33. Trang quản trị tổng quan
Mục đích: Tổng quan hệ thống cho admin.
Đầu vào: -
Đầu ra: Dashboard
Luồng xử lý: Hiển thị cards: users, listings, pending, reports.
Ràng buộc & thông báo lỗi: Phân trang danh sách
Kết quả mong đợi: Dễ nắm tình hình trong 1 trang.
STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Không nhập từ khóa và nhấn tìm kiếm
Trường search bắt buộc, không được để trống
“Vui lòng nhập từ khóa tìm kiếm.”
2
Nhập từ khóa chỉ toàn khoảng trắng
Loại bỏ ký tự trống trước khi xử lý
“Từ khóa không hợp lệ.”
3
Nhập từ khóa vượt quá 100 ký tự
Giới hạn VARCHAR(100)
“Từ khóa tìm kiếm không được vượt quá 100 ký tự.”
4
Nhập từ khóa chứa ký tự đặc biệt
Chỉ cho phép chữ, số và dấu cách
“Từ khóa không được chứa ký tự đặc biệt.”
5
Nhập từ khóa không tồn tại trong dữ liệu
Không có kết quả trả về từ API
“Không tìm thấy kết quả phù hợp.”
6
Nhập ký tự tiếng Việt có dấu nhưng hệ thống không hỗ trợ tìm kiếm có dấu
Nếu hệ thống không xử lý Unicode
“Không tìm thấy kết quả (vui lòng thử lại với không dấu).”
7
Gọi API tìm kiếm lỗi / timeout
Lỗi kết nối hoặc server
“Không thể thực hiện tìm kiếm, vui lòng thử lại.”
8
Kết quả trả về rỗng nhưng vẫn hiển thị phân trang
Không có dữ liệu phù hợp
“Không có dữ liệu để hiển thị.”


34. Upload & tối ưu ảnh (resize, webp)

Hình 23 Giao diện upload & tối ưu ảnh
Mục đích: Tăng tốc hiển thị ảnh.
Đầu vào: image file
Đầu ra: Nhiều kích cỡ + webp
Luồng xử lý: Upload → tạo thumbnail → lưu storage.
Ràng buộc & thông báo lỗi: Giới hạn kích thước
Kết quả mong đợi: CLS/LCP cải thiện rõ rệt.



STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Không chọn file ảnh trước khi upload
Bắt buộc có image
“Vui lòng chọn ít nhất một ảnh để tải lên.”
2
Upload file không đúng định dạng (vd: .exe, .pdf)
Chỉ cho phép .jpg, .jpeg, .png, .webp
“Định dạng file không hợp lệ. Vui lòng chọn file ảnh.”
3
Dung lượng ảnh vượt quá giới hạn
Tối đa 5MB
“Dung lượng ảnh vượt quá giới hạn cho phép (5MB).”
4
Kích thước ảnh quá nhỏ (ví dụ < 50x50px)
Tối thiểu 50x50px
“Ảnh quá nhỏ, vui lòng chọn ảnh có kích thước lớn hơn.”
5
Lỗi trong quá trình tạo thumbnail
Lỗi xử lý server
“Không thể tạo thumbnail, vui lòng thử lại sau.”
6
Lỗi trong quá trình chuyển đổi sang .webp
Không tạo được bản nén
“Không thể tạo phiên bản WebP, vui lòng thử lại.”
7
Lỗi lưu file lên storage
Ghi file thất bại
“Không thể lưu ảnh, vui lòng kiểm tra dung lượng lưu trữ.”
8
Upload đồng thời quá nhiều ảnh
Giới hạn 5 ảnh/lần
“Chỉ được upload tối đa 5 ảnh mỗi lần.”
9
Tên file ảnh chứa ký tự đặc biệt hoặc khoảng trắng
Ràng buộc tên file hợp lệ (a–z, 0–9, gạch dưới)
“Tên file ảnh không hợp lệ.”

35. Trung tâm hỗ trợ & xử lý tranh chấp (Dispute Center)
Mục đích: Chia nhỏ dữ liệu quản trị (users, listings, reports) thành nhiều trang để admin dễ quản lý.
Đầu vào: page, limit, sort_by, filter_status, search_term, date_range
Đầu ra: Danh sách records + pagination controls + bulk actions + export options
Luồng xử lý: Apply filters + Sort data + Query với OFFSET/LIMIT + Render admin table + Enable bulk operations
Ràng buộc & thông báo lỗi: Admin role required, limit 10-100, "Không có dữ liệu phù hợp"
Kết quả mong đợi: Load < 1s với data lớn, sortable columns, bulk select/delete

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Không có quyền admin truy cập trang
Kiểm tra user.role !== 'admin'
"Bạn không có quyền truy cập trang này."
2
Nhập limit ngoài khoảng cho phép
limit phải trong khoảng 10–100
"Giới hạn hiển thị phải nằm trong khoảng 10–100."
3
Không chọn trạng thái (filter_status) mà nhấn tìm kiếm
filter_status không được null nếu đã chọn tìm kiếm nâng cao
"Vui lòng chọn trạng thái cần lọc."
4
Nhập search_term quá dài
search_term VARCHAR(100)
"Từ khóa tìm kiếm không được vượt quá 100 ký tự."
5
Không có dữ liệu phù hợp
Không có record nào trong DB thỏa điều kiện lọc
"Không có dữ liệu phù hợp."
6
Gửi yêu cầu bulk delete mà không chọn bản ghi
selected_ids.length == 0
"Vui lòng chọn ít nhất một bản ghi để thực hiện hành động."
7
Xóa dispute không tồn tại
ID không tồn tại trong DB
"Không tìm thấy tranh chấp cần xóa."
8
Xuất dữ liệu khi không có kết quả
Không có bản ghi để export
"Không có dữ liệu để xuất báo cáo."
9
Lỗi khi kết nối DB hoặc query timeout
Lỗi hệ thống (DB error, timeout > 5s)
"Không thể tải dữ liệu. Vui lòng thử lại sau."
10
Nhập khoảng thời gian không hợp lệ (date_range)
start_date > end_date
"Khoảng thời gian không hợp lệ."

36. Trang FAQ & Liên hệ

Hình 24 Giao diện trang FAQ

Hình 25 Giao diện trang Liên Hệ
Mục đích: Giải đáp và kênh liên hệ nhà trường.
Đầu vào: faqs[], form contact
Đầu ra: Danh sách FAQs, email gửi
Luồng xử lý: CRUD FAQs; gửi mail từ form.
Ràng buộc & thông báo lỗi: Rate limit gửi liên hệ
Kết quả mong đợi: Thông tin gửi thành công, có phản hồi.




STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Nhập ID trống hoặc không tồn tại
id bắt buộc, phải tồn tại trong DB (nếu liên kết user)
"Vui lòng nhập ID hợp lệ."
2
Nhập họ và tên trống
Trường fullname bắt buộc nhập
"Vui lòng nhập họ và tên của bạn."
3
Nhập họ và tên vượt quá 100 ký tự
fullname VARCHAR(100)
"Họ và tên không được vượt quá 100 ký tự."
4
Nhập số điện thoại sai định dạng
Kiểm tra regex ^0\d{9,10}$
"Số điện thoại không hợp lệ."
5
Email trống hoặc sai định dạng
Kiểm tra regex email hợp lệ
"Vui lòng nhập email đúng định dạng."
6
Ô “Nhập ý kiến cá nhân” để trống
Trường message bắt buộc
"Vui lòng nhập nội dung phản hồi."
7
Ô “Nhập ý kiến cá nhân” quá dài
Giới hạn 500 ký tự
"Nội dung phản hồi không được vượt quá 500 ký tự."
8
Gửi phản hồi liên tục nhiều lần
Giới hạn 3 lần/5 phút (rate limit)
"Bạn đang gửi phản hồi quá nhanh. Vui lòng thử lại sau ít phút."
9
Lỗi khi gửi dữ liệu lên server (500)
Lỗi server hoặc mạng
"Không thể gửi phản hồi lúc này. Vui lòng thử lại sau."
10
Gửi thành công
Gửi phản hồi hợp lệ
"Cảm ơn bạn đã gửi ý kiến, chúng tôi sẽ phản hồi sớm nhất!"

37. Quản lý thông báo hệ thống
Mục đích: Đẩy announcement toàn hệ thống.
Đầu vào: title, body, audience
Đầu ra: announcement record
Luồng xử lý: Create → phát hành → hiển thị banner/in-app.
Ràng buộc & thông báo lỗi: Hết hạn tự ẩn
Kết quả mong đợi: Hiển thị đúng phạm vi (toàn trường/nhóm).

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Tạo thông báo mới nhưng không nhập tiêu đề (title)
Bắt buộc nhập, VARCHAR(150)
"Vui lòng nhập tiêu đề thông báo."
2
Tiêu đề vượt quá 150 ký tự
title VARCHAR(150)
"Tiêu đề thông báo không được vượt quá 150 ký tự."
3
Không nhập nội dung thông báo (body)
Trường body bắt buộc
"Vui lòng nhập nội dung thông báo."
4
Nội dung vượt quá 2000 ký tự
body TEXT(2000)
"Nội dung thông báo không được vượt quá 2000 ký tự."
5
Không chọn phạm vi hiển thị (audience)
Bắt buộc chọn (toàn trường / nhóm / người dùng cụ thể)
"Vui lòng chọn phạm vi hiển thị thông báo."
6
Chọn phạm vi người dùng nhưng danh sách người nhận trống
Khi audience = custom thì phải có user_ids[]
"Vui lòng chọn ít nhất một người nhận thông báo."
7
Đặt thời gian hết hạn nhỏ hơn thời gian hiện tại
expired_at > NOW()
"Thời gian hết hạn phải sau thời gian hiện tại."
8
Phát hành thông báo nhưng dữ liệu không hợp lệ
Validate form lỗi (missing field)
"Không thể phát hành, dữ liệu chưa hợp lệ."
9
Xóa thông báo không tồn tại trong DB
ID không tồn tại
"Không tìm thấy thông báo cần xóa."
10
Hiển thị banner sau khi hết hạn
expired_at < NOW() thì tự ẩn
(Ẩn banner, không hiển thị thông báo)
11
Tải danh sách thông báo lỗi (API lỗi / DB lỗi)
Lỗi kết nối server
"Không thể tải danh sách thông báo. Vui lòng thử lại sau."
12
Không có thông báo nào trong danh sách
Query trả về rỗng
"Hiện chưa có thông báo nào được đăng."
 

38. Export dữ liệu báo cáo (CSV/Excel)
Mục đích: Kết xuất dữ liệu cho báo cáo.
Đầu vào: filters
Đầu ra: file CSV/XLSX
Luồng xử lý: Query theo filter → ghi file.
Ràng buộc & thông báo lỗi: Giới hạn số dòng; phân quyền
Kết quả mong đợi: File tải xuống đúng định dạng.

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Không chọn bộ lọc dữ liệu trước khi export
Ít nhất 1 filter hợp lệ phải được chọn
"Vui lòng chọn điều kiện lọc trước khi xuất dữ liệu."
2
Dữ liệu truy vấn rỗng (không có kết quả)
Kết quả query = 0 bản ghi
"Không có dữ liệu để xuất báo cáo."
3
Chọn định dạng export không hợp lệ
Chỉ cho phép CSV hoặc XLSX
"Định dạng file xuất không hợp lệ."
4
Dữ liệu xuất vượt quá giới hạn cho phép
Giới hạn tối đa 10.000 dòng / file
"Dữ liệu xuất vượt quá giới hạn cho phép (10.000 dòng). Vui lòng lọc bớt dữ liệu."
5
Người dùng không có quyền export
Role ≠ admin / manager
"Bạn không có quyền thực hiện thao tác này."
6
Lỗi trong quá trình ghi file
Lỗi ghi tạm thời trong storage hoặc bộ nhớ
"Không thể tạo file xuất. Vui lòng thử lại sau."
7
Lỗi tải xuống file
Mất kết nối hoặc file không tồn tại
"Tải file thất bại. Vui lòng thử lại."
8
Export thành công
Dữ liệu hợp lệ, file được tạo
"Xuất dữ liệu thành công. File đang được tải xuống."

39. Trang điều khoản & lưu consent
Mục đích: Lưu vết người dùng đã chấp thuận.
Đầu vào: doc_slug, version
Đầu ra: consent record
Luồng xử lý: Hiển thị modal khi version mới → lưu consent.
Ràng buộc & thông báo lỗi: Chỉ lưu cờ, không lưu nội dung nhạy cảm
Kết quả mong đợi: Truy vấn được ai đã/ chưa đồng ý.

STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Không tải được nội dung điều khoản (terms)
Lỗi truy vấn từ bảng terms_documents
"Không thể tải nội dung điều khoản. Vui lòng thử lại sau."
2
Người dùng đóng modal mà không chọn chấp thuận
consent_flag chưa được chọn
"Vui lòng đọc và chấp thuận điều khoản trước khi tiếp tục."
3
Không có doc_slug khi lưu consent
Bắt buộc gửi doc_slug tương ứng tài liệu
"Thiếu mã tài liệu cần lưu consent."
4
Không có version hoặc version không tồn tại
version phải trùng bản phát hành hiện tại
"Phiên bản điều khoản không hợp lệ."
5
Người dùng chưa đăng nhập nhưng cố lưu consent
Bắt buộc xác thực (user_id tồn tại)
"Bạn cần đăng nhập để lưu chấp thuận."
6
Ghi consent trùng lặp
Đã tồn tại bản ghi consent cho user_id + version
"Bạn đã đồng ý với điều khoản này rồi."
7
Lỗi ghi consent vào database
Lỗi hệ thống khi insert vào user_consents
"Không thể lưu chấp thuận. Vui lòng thử lại sau."
8
Không có phiên bản điều khoản hiện hành
Không tìm thấy bản is_active = 1 trong DB
"Không có điều khoản hợp lệ để hiển thị."
9
Không có consent nào trong truy vấn quản trị
Bảng user_consents trống hoặc query rỗng
"Chưa có người dùng nào chấp thuận điều khoản."
10
Lưu consent thành công
INSERT thành công, dữ liệu hợp lệ
"Cảm ơn bạn đã chấp thuận điều khoản sử dụng."

40. Giám sát lỗi & cảnh báo hệ thống
Mục đích: Theo dõi lỗi/độ trễ.
Đầu vào: level, message, meta
Đầu ra: log/alert
Luồng xử lý: Gửi lỗi về Sentry/Healthcheck → cảnh báo.
Ràng buộc & thông báo lỗi: Ẩn thông tin cá nhân
Kết quả mong đợi: Có trang /healthz và dashboard đơn giản.


STT
Thông tin mô tả lỗi
Chiều dài / Ràng buộc
Thông báo lỗi
1
Không ghi nhận được lỗi (log missing)
Thiếu message hoặc level khi ghi log
"Không thể ghi nhận lỗi. Thông tin log chưa đầy đủ."
2
Gửi log với cấp độ không hợp lệ
level chỉ chấp nhận: info, warning, error, critical
"Cấp độ log không hợp lệ."
3
Ghi log chứa thông tin cá nhân
Hệ thống kiểm tra regex, cấm gửi dữ liệu PII (email, phone, token)
"Log chứa thông tin nhạy cảm, không được phép gửi."
4
Hệ thống không kết nối được với Sentry / Healthcheck API
Timeout > 5s hoặc mất kết nối mạng
"Không thể gửi cảnh báo. Kiểm tra kết nối hệ thống giám sát."
5
Dashboard lỗi khi load danh sách logs
Lỗi query DB hoặc API timeout
"Không thể tải dữ liệu giám sát. Vui lòng thử lại sau."
6
Không có dữ liệu log nào trong khoảng thời gian chọn
Query rỗng theo filter thời gian
"Không có bản ghi lỗi nào trong khoảng thời gian này."
7
Truy cập trang /healthz mà hệ thống mất kết nối database
Healthcheck trả về HTTP 500
"Dịch vụ không khả dụng (Database lỗi)."
8
Healthcheck trả về status không đúng
Phản hồi khác 200 OK
"Hệ thống có sự cố. Vui lòng kiểm tra máy chủ."
9
Gửi cảnh báo trùng lặp
Kiểm tra hash(message+timestamp) trong 5 phút
"Cảnh báo này đã được ghi nhận, không gửi lại."
10
Gửi log thành công
Log hợp lệ, gửi đến Sentry / Healthcheck thành công
"Đã ghi nhận lỗi và gửi cảnh báo thành công."

PHẦN 3. TÀI LIỆU THAM KHẢO
[1] OpenAI Chat GPT: https://www.openai.com
[2] File báo cáo của SV khóa trước. (2024)