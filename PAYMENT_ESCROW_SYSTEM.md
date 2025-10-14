# 💰 HỆ THỐNG THANH TOÁN ESCROW CHO TDC MARKETPLACE

## 🎯 **TỔNG QUAN HỆ THỐNG**

### **🔒 Mô hình Escrow (Ký quỹ)**
- **Người mua** thanh toán → **Hệ thống giữ tiền** (Escrow)
- **Người bán** giao hàng → **Người mua xác nhận** nhận hàng
- **Sau 3 ngày** → **Hệ thống chuyển tiền** cho người bán
- **Nếu có tranh chấp** → **Hệ thống giữ tiền** để giải quyết

---

## 🏗️ **THIẾT KẾ DATABASE CHO 3 CHỨC NĂNG**

### **1. 🛒 Bảng `orders` - Đơn hàng**

```sql
CREATE TABLE orders (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_number VARCHAR(50) NOT NULL UNIQUE,
  buyer_id BIGINT UNSIGNED NOT NULL,
  seller_id BIGINT UNSIGNED NOT NULL,
  listing_id BIGINT UNSIGNED NOT NULL,
  offer_id BIGINT UNSIGNED NULL,
  
  -- Thông tin sản phẩm
  product_title VARCHAR(255) NOT NULL,
  product_price DECIMAL(12,2) NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  total_amount DECIMAL(12,2) NOT NULL,
  currency VARCHAR(3) NOT NULL DEFAULT 'VND',
  
  -- Trạng thái đơn hàng
  status ENUM(
    'pending',           -- Chờ thanh toán
    'paid',              -- Đã thanh toán (tiền trong escrow)
    'confirmed',         -- Người bán xác nhận đơn hàng
    'shipped',           -- Đã giao hàng
    'delivered',         -- Người mua xác nhận nhận hàng
    'completed',         -- Hoàn thành (tiền chuyển cho người bán)
    'cancelled',         -- Hủy đơn hàng
    'disputed',          -- Có tranh chấp
    'refunded'           -- Hoàn tiền
  ) NOT NULL DEFAULT 'pending',
  
  -- Thông tin giao hàng
  pickup_point_id BIGINT UNSIGNED NULL,
  delivery_method ENUM('pickup','delivery') NOT NULL DEFAULT 'pickup',
  delivery_address TEXT NULL,
  delivery_notes TEXT NULL,
  
  -- Thời gian quan trọng
  paid_at TIMESTAMP NULL,
  confirmed_at TIMESTAMP NULL,
  shipped_at TIMESTAMP NULL,
  delivered_at TIMESTAMP NULL,
  completed_at TIMESTAMP NULL,
  cancelled_at TIMESTAMP NULL,
  
  -- Thông tin bổ sung
  notes TEXT NULL,
  admin_notes TEXT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (id),
  KEY idx_orders_buyer (buyer_id),
  KEY idx_orders_seller (seller_id),
  KEY idx_orders_status (status),
  KEY idx_orders_paid (paid_at),
  KEY idx_orders_completed (completed_at),
  
  CONSTRAINT fk_orders_buyer
    FOREIGN KEY (buyer_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_orders_seller
    FOREIGN KEY (seller_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_orders_listing
    FOREIGN KEY (listing_id) REFERENCES listings(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_orders_offer
    FOREIGN KEY (offer_id) REFERENCES offers(id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_orders_pickup
    FOREIGN KEY (pickup_point_id) REFERENCES campus_pickups(id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### **2. 💳 Bảng `payments` - Thanh toán**

```sql
CREATE TABLE payments (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id BIGINT UNSIGNED NOT NULL,
  payment_method ENUM('momo','vnpay','zalopay','bank_transfer','cash') NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  currency VARCHAR(3) NOT NULL DEFAULT 'VND',
  
  -- Trạng thái thanh toán
  status ENUM(
    'pending',           -- Chờ thanh toán
    'processing',        -- Đang xử lý
    'completed',         -- Thanh toán thành công
    'failed',            -- Thanh toán thất bại
    'cancelled',         -- Hủy thanh toán
    'refunded',          -- Đã hoàn tiền
    'refund_pending'     -- Chờ hoàn tiền
  ) NOT NULL DEFAULT 'pending',
  
  -- Thông tin giao dịch
  transaction_id VARCHAR(100) NULL,
  gateway_response JSON NULL,
  refund_transaction_id VARCHAR(100) NULL,
  
  -- Thời gian
  processed_at TIMESTAMP NULL,
  refunded_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (id),
  KEY idx_payments_order (order_id),
  KEY idx_payments_status (status),
  KEY idx_payments_transaction (transaction_id),
  
  CONSTRAINT fk_payments_order
    FOREIGN KEY (order_id) REFERENCES orders(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### **3. 🔄 Bảng `order_confirmations` - Xác nhận giao dịch**

```sql
CREATE TABLE order_confirmations (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id BIGINT UNSIGNED NOT NULL,
  type ENUM('seller_confirm','buyer_confirm','delivery_confirm','dispute') NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  
  -- Nội dung xác nhận
  status ENUM('confirmed','rejected','pending') NOT NULL DEFAULT 'pending',
  message TEXT NULL,
  evidence JSON NULL, -- Hình ảnh, video chứng minh
  
  -- Thông tin giao hàng (nếu là delivery_confirm)
  delivery_proof VARCHAR(255) NULL, -- Hình ảnh giao hàng
  delivery_notes TEXT NULL,
  
  -- Thời gian
  confirmed_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (id),
  KEY idx_confirmations_order (order_id),
  KEY idx_confirmations_type (type),
  KEY idx_confirmations_user (user_id),
  
  CONSTRAINT fk_confirmations_order
    FOREIGN KEY (order_id) REFERENCES orders(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_confirmations_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 🔄 **QUY TRÌNH XỬ LÝ ĐƠN HÀNG**

### **📋 Bước 1: Tạo đơn hàng**
```php
// Khi người mua tạo đơn hàng
$order = Order::create([
    'order_number' => 'ORD' . time() . rand(1000, 9999),
    'buyer_id' => $buyer->id,
    'seller_id' => $listing->seller_id,
    'listing_id' => $listing->id,
    'product_title' => $listing->title,
    'product_price' => $listing->price,
    'total_amount' => $listing->price,
    'status' => 'pending'
]);
```

### **💳 Bước 2: Thanh toán**
```php
// Khi thanh toán thành công
$payment = Payment::create([
    'order_id' => $order->id,
    'payment_method' => 'momo',
    'amount' => $order->total_amount,
    'status' => 'completed',
    'transaction_id' => $gateway_response['transaction_id'],
    'processed_at' => now()
]);

// Cập nhật trạng thái đơn hàng
$order->update([
    'status' => 'paid',
    'paid_at' => now()
]);
```

### **✅ Bước 3: Người bán xác nhận đơn hàng**
```php
// Người bán xác nhận đơn hàng
OrderConfirmation::create([
    'order_id' => $order->id,
    'type' => 'seller_confirm',
    'user_id' => $seller->id,
    'status' => 'confirmed',
    'message' => 'Đã xác nhận đơn hàng, sẽ giao hàng trong 24h',
    'confirmed_at' => now()
]);

$order->update([
    'status' => 'confirmed',
    'confirmed_at' => now()
]);
```

### **🚚 Bước 4: Giao hàng**
```php
// Người bán báo đã giao hàng
OrderConfirmation::create([
    'order_id' => $order->id,
    'type' => 'delivery_confirm',
    'user_id' => $seller->id,
    'status' => 'confirmed',
    'delivery_proof' => 'delivery_image.jpg',
    'delivery_notes' => 'Đã giao hàng tại điểm hẹn',
    'confirmed_at' => now()
]);

$order->update([
    'status' => 'shipped',
    'shipped_at' => now()
]);
```

### **✅ Bước 5: Người mua xác nhận nhận hàng**
```php
// Người mua xác nhận nhận hàng
OrderConfirmation::create([
    'order_id' => $order->id,
    'type' => 'buyer_confirm',
    'user_id' => $buyer->id,
    'status' => 'confirmed',
    'message' => 'Đã nhận hàng, sản phẩm đúng như mô tả',
    'confirmed_at' => now()
]);

$order->update([
    'status' => 'delivered',
    'delivered_at' => now()
]);

// Tự động chuyển sang completed sau 3 ngày
$order->update([
    'status' => 'completed',
    'completed_at' => now()->addDays(3)
]);
```

---

## ⚠️ **XỬ LÝ CÁC TRƯỜNG HỢP ĐẶC BIỆT**

### **🚨 Trường hợp 1: Người bán xác nhận nhưng không giao hàng**

```php
// Sau 48h kể từ khi xác nhận mà chưa giao hàng
if ($order->status === 'confirmed' && 
    $order->confirmed_at->diffInHours(now()) > 48) {
    
    // Tự động chuyển sang disputed
    $order->update(['status' => 'disputed']);
    
    // Tạo báo cáo tự động
    Report::create([
        'reporter_id' => $order->buyer_id,
        'reportable_type' => 'App\\Models\\Order',
        'reportable_id' => $order->id,
        'reason_category' => 'seller_not_deliver',
        'reason' => 'Người bán xác nhận đơn hàng nhưng không giao hàng sau 48h',
        'status' => 'pending'
    ]);
    
    // Thông báo cho admin
    Notification::create([
        'user_id' => 1, // Admin ID
        'type' => 'order_disputed',
        'title' => 'Đơn hàng bị tranh chấp',
        'message' => "Đơn hàng {$order->order_number} bị tranh chấp do người bán không giao hàng"
    ]);
}
```

### **🚨 Trường hợp 2: Người mua không xác nhận nhận hàng**

```php
// Sau 7 ngày kể từ khi giao hàng mà chưa xác nhận
if ($order->status === 'shipped' && 
    $order->shipped_at->diffInDays(now()) > 7) {
    
    // Tự động chuyển sang completed
    $order->update([
        'status' => 'completed',
        'completed_at' => now()
    ]);
    
    // Chuyển tiền cho người bán
    $this->transferToSeller($order);
}
```

### **🚨 Trường hợp 3: Tranh chấp**

```php
// Khi có tranh chấp
public function createDispute($orderId, $userId, $reason) {
    $order = Order::find($orderId);
    
    // Tạo dispute
    Dispute::create([
        'order_id' => $orderId,
        'opener_id' => $userId,
        'reason' => $reason,
        'status' => 'open'
    ]);
    
    // Cập nhật trạng thái đơn hàng
    $order->update(['status' => 'disputed']);
    
    // Giữ tiền trong escrow cho đến khi giải quyết
    // Không chuyển tiền cho người bán
}
```

---

## 💰 **QUẢN LÝ TIỀN ESCROW**

### **🏦 Bảng `escrow_accounts` - Tài khoản ký quỹ**

```sql
CREATE TABLE escrow_accounts (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id BIGINT UNSIGNED NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  currency VARCHAR(3) NOT NULL DEFAULT 'VND',
  status ENUM('held','released','refunded') NOT NULL DEFAULT 'held',
  released_at TIMESTAMP NULL,
  refunded_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (id),
  KEY idx_escrow_order (order_id),
  KEY idx_escrow_status (status),
  
  CONSTRAINT fk_escrow_order
    FOREIGN KEY (order_id) REFERENCES orders(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### **💸 Chuyển tiền cho người bán**

```php
public function transferToSeller($order) {
    // Tạo escrow account
    EscrowAccount::create([
        'order_id' => $order->id,
        'amount' => $order->total_amount,
        'status' => 'released',
        'released_at' => now()
    ]);
    
    // Cập nhật tài khoản người bán
    $seller = User::find($order->seller_id);
    $seller->increment('balance', $order->total_amount);
    
    // Ghi log
    AuditLog::create([
        'user_id' => $order->seller_id,
        'action' => 'escrow_released',
        'auditable_type' => 'App\\Models\\Order',
        'auditable_id' => $order->id,
        'new_values' => ['amount' => $order->total_amount]
    ]);
}
```

---

## 🎯 **TÓM TẮT GIẢI PHÁP**

### **✅ Ưu điểm:**
1. **Bảo vệ người mua:** Tiền được giữ trong escrow
2. **Bảo vệ người bán:** Tự động chuyển tiền sau 3 ngày
3. **Xử lý tranh chấp:** Hệ thống giữ tiền khi có dispute
4. **Tự động hóa:** Giảm thiểu can thiệp thủ công

### **🔒 Bảo mật:**
1. **Escrow system:** Tiền không về tay người bán ngay
2. **Time-based release:** Tự động chuyển tiền sau 3 ngày
3. **Dispute handling:** Giữ tiền khi có tranh chấp
4. **Audit trail:** Ghi log mọi giao dịch

### **📱 Trải nghiệm người dùng:**
1. **Rõ ràng:** Người dùng biết tiền ở đâu
2. **Minh bạch:** Có thể theo dõi trạng thái đơn hàng
3. **An toàn:** Được bảo vệ khỏi lừa đảo
4. **Nhanh chóng:** Tự động hóa quy trình

**🎉 Hệ thống này đảm bảo an toàn cho cả người mua và người bán!**
