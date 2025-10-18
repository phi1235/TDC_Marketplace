<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ListingSeeder extends Seeder
{
    public function run(): void
    {

        $seller = User::where('role', 'user')->first() ?? User::first();
        if (!$seller) { return; }

        // Approved sample
        Listing::create([
            'seller_id' => $seller->id,
            'category_id' => 1,
            'title' => 'Laptop học tập 14 inch',
            'description' => 'Máy chạy tốt, pin 5h.',
            'price' => 7500000,
            'condition' => 'good',
            'status' => 'approved',
            'location' => 'TDC Campus',
            'views_count' => 10,
            'approved_at' => now(),
            'approved_by' => User::where('role', 'admin')->value('id'),
        ]);

        // Pending sample
        $pendingSamples = [
            ['Giáo trình toán cao cấp', 'Sách còn mới 90%.', 120000],
            ['Tai nghe không dây', 'Âm thanh tốt, pin bền.', 350000],
            ['Balo sinh viên', 'Balo chống nước, còn mới.', 200000],
        ];

        foreach ($pendingSamples as [$title, $desc, $price]) {
            Listing::create([
                'seller_id' => $seller->id,
                'category_id' => 1,
                'title' => $title,
                'description' => $desc,
                'price' => $price,
                'condition' => 'good',
                'status' => 'pending',
                'location' => 'Khu A',
                'views_count' => 0,
            ]);
        }

        // Rejected sample
        Listing::create([
            'seller_id' => $seller->id,
            'category_id' => 1,
            'title' => 'Điện thoại cũ',
            'description' => 'Máy xước nhẹ.',
            'price' => 900000,
            'condition' => 'fair',
            'status' => 'rejected',
            'location' => 'Khu B',
            'views_count' => 0,
            'rejected_at' => now(),
            'rejected_by' => User::where('role', 'admin')->value('id'),
            'rejection_reason' => 'policy_violation',
        ]);
    }
}


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
