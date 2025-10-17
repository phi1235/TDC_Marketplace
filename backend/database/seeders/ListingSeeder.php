<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ListingSeeder extends Seeder
{
    public function run(): void
    {
        // Tạm tắt kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Xóa bảng con trước (nếu có)
        DB::table('listing_images')->truncate();

        // Xóa bảng listings
        DB::table('listings')->truncate();

        // Bật lại kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Thêm dữ liệu mẫu
        DB::table('listings')->insert([
            [
                'seller_id' => 1,
                'category_id' => 1,
                'title' => 'Sách Giáo Khoa Toán 12',
                'description' => 'Bộ sách giáo khoa Toán lớp 12 – bản chuẩn của Bộ GD&ĐT.',
                'condition' => 'like_new',
                'price' => 40000,
                'status' => 'approved',
                'location' => 'Thủ Đức, TP.HCM',
                'views_count' => 125,
                'approved_at' => now(),
                'approved_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'seller_id' => 2,
                'category_id' => 2,
                'title' => 'Laptop Dell Inspiron Cũ',
                'description' => 'Laptop Dell cũ phù hợp cho sinh viên học CNTT, cấu hình Core i5, SSD 256GB.',
                'condition' => 'used',
                'price' => 3500000,
                'status' => 'approved',
                'location' => 'Quận 9, TP.HCM',
                'views_count' => 232,
                'approved_at' => now(),
                'approved_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'seller_id' => 3,
                'category_id' => 3,
                'title' => 'Bút bi Thiên Long TL-027',
                'description' => 'Bút bi Thiên Long TL-027 màu xanh, mực đậm, viết êm.',
                'condition' => 'new',
                'price' => 5000,
                'status' => 'approved',
                'location' => 'Thủ Đức, TP.HCM',
                'views_count' => 78,
                'approved_at' => now(),
                'approved_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'seller_id' => 4,
                'category_id' => 4,
                'title' => 'Áo khoác Khoa CNTT TDC',
                'description' => 'Áo khoác sinh viên TDC, màu xanh đen, vải thun lạnh thoáng mát.',
                'condition' => 'new',
                'price' => 120000,
                'status' => 'approved',
                'location' => 'Dĩ An, Bình Dương',
                'views_count' => 98,
                'approved_at' => now(),
                'approved_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
