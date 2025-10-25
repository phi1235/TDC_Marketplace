-- Script to generate 1000 random listings for TDC Marketplace
-- Run this directly in phpMyAdmin

-- First, ensure we have users and categories
INSERT IGNORE INTO users (name, email, phone, password, role, is_active, email_verified_at, created_at, updated_at) VALUES
('User 1', 'user1@tdc.edu.vn', '01234567801', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 1, NOW(), NOW(), NOW()),
('User 2', 'user2@tdc.edu.vn', '01234567802', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 1, NOW(), NOW(), NOW()),
('User 3', 'user3@tdc.edu.vn', '01234567803', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 1, NOW(), NOW(), NOW()),
('User 4', 'user4@tdc.edu.vn', '01234567804', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 1, NOW(), NOW(), NOW()),
('User 5', 'user5@tdc.edu.vn', '01234567805', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 1, NOW(), NOW(), NOW()),
('User 6', 'user6@tdc.edu.vn', '01234567806', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 1, NOW(), NOW(), NOW()),
('User 7', 'user7@tdc.edu.vn', '01234567807', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 1, NOW(), NOW(), NOW()),
('User 8', 'user8@tdc.edu.vn', '01234567808', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 1, NOW(), NOW(), NOW()),
('User 9', 'user9@tdc.edu.vn', '01234567809', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 1, NOW(), NOW(), NOW()),
('User 10', 'user10@tdc.edu.vn', '01234567810', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 1, NOW(), NOW(), NOW());

-- Insert categories if they don't exist
INSERT IGNORE INTO categories (name, slug, description, icon, is_active, created_at, updated_at) VALUES
('Sách giáo khoa', 'sach-giao-khoa', 'Danh mục Sách giáo khoa', 'book', 1, NOW(), NOW()),
('Điện tử', 'dien-tu', 'Danh mục Điện tử', 'laptop', 1, NOW(), NOW()),
('Quần áo', 'quan-ao', 'Danh mục Quần áo', 'shirt', 1, NOW(), NOW()),
('Đồ chơi', 'do-choi', 'Danh mục Đồ chơi', 'game', 1, NOW(), NOW()),
('Nhạc cụ', 'nhac-cu', 'Danh mục Nhạc cụ', 'music', 1, NOW(), NOW()),
('Thể thao', 'the-thao', 'Danh mục Thể thao', 'sport', 1, NOW(), NOW()),
('Mỹ phẩm', 'my-pham', 'Danh mục Mỹ phẩm', 'beauty', 1, NOW(), NOW()),
('Thực phẩm', 'thuc-pham', 'Danh mục Thực phẩm', 'food', 1, NOW(), NOW()),
('Nội thất', 'noi-that', 'Danh mục Nội thất', 'furniture', 1, NOW(), NOW()),
('Xe cộ', 'xe-co', 'Danh mục Xe cộ', 'car', 1, NOW(), NOW());

-- Generate random listings using a stored procedure approach
-- This will create 1000 random listings with random data

DELIMITER $$

CREATE PROCEDURE GenerateRandomListings()
BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE user_count INT;
    DECLARE category_count INT;
    DECLARE random_user_id INT;
    DECLARE random_category_id INT;
    DECLARE random_price INT;
    DECLARE random_views INT;
    DECLARE random_condition VARCHAR(20);
    DECLARE random_status VARCHAR(20);
    DECLARE random_location VARCHAR(50);
    DECLARE random_title VARCHAR(255);
    DECLARE random_description TEXT;
    DECLARE random_created_at DATETIME;
    
    -- Get counts
    SELECT COUNT(*) INTO user_count FROM users WHERE role = 'user';
    SELECT COUNT(*) INTO category_count FROM categories;
    
    -- Generate 1000 listings
    WHILE i < 1000 DO
        -- Get random user and category
        SELECT id INTO random_user_id FROM users WHERE role = 'user' ORDER BY RAND() LIMIT 1;
        SELECT id INTO random_category_id FROM categories ORDER BY RAND() LIMIT 1;
        
        -- Generate random data
        SET random_price = FLOOR(RAND() * 49900000) + 10000; -- 10k to 50M
        SET random_views = FLOOR(RAND() * 1001); -- 0 to 1000
        SET random_condition = ELT(FLOOR(RAND() * 4) + 1, 'new', 'like_new', 'good', 'fair');
        SET random_status = ELT(FLOOR(RAND() * 3) + 1, 'approved', 'pending', 'rejected');
        SET random_location = ELT(FLOOR(RAND() * 8) + 1, 'Hà Nội', 'TP.HCM', 'Đà Nẵng', 'Cần Thơ', 'Hải Phòng', 'Nha Trang', 'Huế', 'Vũng Tàu');
        
        -- Generate random title
        SET random_title = CONCAT(
            ELT(FLOOR(RAND() * 6) + 1, 'Bán', 'Cần bán', 'Thanh lý', 'Bán gấp', 'Bán rẻ', 'Tặng kèm'), ' ',
            ELT(FLOOR(RAND() * 10) + 1, 'Laptop', 'Điện thoại', 'Sách', 'Quần áo', 'Giày dép', 'Đồ chơi', 'Nhạc cụ', 'Thể thao', 'Mỹ phẩm', 'Thực phẩm'), ' ',
            ELT(FLOOR(RAND() * 8) + 1, 'cũ', 'mới', 'đẹp', 'tốt', 'rẻ', 'cao cấp', 'hiếm', 'độc đáo')
        );
        
        -- Generate random description
        SET random_description = ELT(FLOOR(RAND() * 10) + 1,
            'Sản phẩm chất lượng tốt, còn mới 90%. Phù hợp cho sinh viên, người đi làm.',
            'Đã sử dụng ít, tình trạng rất tốt. Bán vì không còn nhu cầu sử dụng.',
            'Sản phẩm chính hãng, có hóa đơn mua hàng. Bảo hành còn lại 6 tháng.',
            'Mua về dùng thử, không phù hợp nên bán lại. Giá rẻ hơn thị trường.',
            'Đồ cũ nhưng còn sử dụng tốt. Phù hợp cho người có thu nhập thấp.',
            'Sản phẩm cao cấp, ít sử dụng. Bán vì chuyển nhà, không mang theo được.',
            'Hàng thanh lý, giá rẻ. Còn sử dụng được, phù hợp cho sinh viên.',
            'Đồ cũ nhưng chất lượng tốt. Bán vì không còn nhu cầu sử dụng.',
            'Sản phẩm mới 100%, chưa sử dụng. Có hóa đơn mua hàng đầy đủ.',
            'Đồ cũ nhưng còn sử dụng tốt. Giá rẻ, phù hợp cho người có thu nhập thấp.'
        );
        
        -- Generate random created_at (last 6 months)
        SET random_created_at = DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 180) DAY);
        
        -- Insert listing
        INSERT INTO listings (seller_id, category_id, title, description, condition, price, status, location, views_count, created_at, updated_at)
        VALUES (random_user_id, random_category_id, random_title, random_description, random_condition, random_price, random_status, random_location, random_views, random_created_at, NOW());
        
        SET i = i + 1;
    END WHILE;
END$$

DELIMITER ;

-- Call the procedure
CALL GenerateRandomListings();

-- Drop the procedure
DROP PROCEDURE GenerateRandomListings;

-- Generate random images for the listings
-- This will add 1-3 random images for each listing
INSERT INTO listing_images (listing_id, image_path, original_name, file_size, mime_type, width, height, is_primary, sort_order, created_at, updated_at)
SELECT 
    l.id as listing_id,
    CONCAT('listings/', DATE_FORMAT(l.created_at, '%Y/%m/%d'), '/', UUID(), '.jpg') as image_path,
    CONCAT('sample_', FLOOR(RAND() * 1000), '_', FLOOR(RAND() * 1000), '.jpg') as original_name,
    FLOOR(RAND() * 450000) + 50000 as file_size,
    'image/jpeg' as mime_type,
    FLOOR(RAND() * 500) + 300 as width,
    FLOOR(RAND() * 300) + 300 as height,
    CASE WHEN ROW_NUMBER() OVER (PARTITION BY l.id ORDER BY RAND()) = 1 THEN 1 ELSE 0 END as is_primary,
    ROW_NUMBER() OVER (PARTITION BY l.id ORDER BY RAND()) - 1 as sort_order,
    l.created_at,
    NOW()
FROM listings l
CROSS JOIN (
    SELECT 1 as n UNION SELECT 2 UNION SELECT 3
) numbers
WHERE RAND() < 0.7; -- 70% chance of having an image

-- Update the first image of each listing to be primary
UPDATE listing_images li1
SET is_primary = 1
WHERE li1.id IN (
    SELECT MIN(li2.id)
    FROM listing_images li2
    WHERE li2.listing_id = li1.listing_id
    GROUP BY li2.listing_id
);

-- Set all other images to not primary
UPDATE listing_images li1
SET is_primary = 0
WHERE li1.id NOT IN (
    SELECT MIN(li2.id)
    FROM listing_images li2
    WHERE li2.listing_id = li1.listing_id
    GROUP BY li2.listing_id
);
