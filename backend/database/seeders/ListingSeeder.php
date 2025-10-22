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
        // Tạm tắt kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Xóa bảng con trước (nếu có)
        DB::table('listing_images')->truncate();

        // Xóa bảng listings
        DB::table('listings')->truncate();

        // Bật lại kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Tạo dữ liệu mẫu với Eloquent
        $seller = User::where('role', 'user')->first() ?? User::first();
        if (!$seller) { 
            return; 
        }

        // Approved samples
        $approvedSamples = [
            [
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
                'approved_by' => User::where('role', 'admin')->value('id') ?? 1,
            ],
            [
                'seller_id' => $seller->id,
                'category_id' => 1,
                'title' => 'Sách Giáo Khoa Toán 12',
                'description' => 'Bộ sách giáo khoa Toán lớp 12 – bản chuẩn của Bộ GD&ĐT.',
                'condition' => 'like_new',
                'price' => 40000,
                'status' => 'approved',
                'location' => 'Thủ Đức, TP.HCM',
                'views_count' => 125,
                'approved_at' => now(),
                'approved_by' => User::where('role', 'admin')->value('id') ?? 1,
            ],
            [
                'seller_id' => $seller->id,
                'category_id' => 2,
                'title' => 'Laptop Dell Inspiron Cũ',
                'description' => 'Laptop Dell cũ phù hợp cho sinh viên học CNTT, cấu hình Core i5, SSD 256GB.',
                'condition' => 'used',
                'price' => 3500000,
                'status' => 'approved',
                'location' => 'Quận 9, TP.HCM',
                'views_count' => 232,
                'approved_at' => now(),
                'approved_by' => User::where('role', 'admin')->value('id') ?? 1,
            ],
            [
                'seller_id' => $seller->id,
                'category_id' => 1,
                'title' => 'Bút bi Thiên Long TL-027',
                'description' => 'Bút bi Thiên Long TL-027 màu xanh, mực đậm, viết êm.',
                'price' => 5000,
                'condition' => 'new',
                'status' => 'approved',
                'location' => 'Thủ Đức, TP.HCM',
                'views_count' => 78,
                'approved_at' => now(),
                'approved_by' => User::where('role', 'admin')->value('id') ?? 1,
            ],
            [
                'seller_id' => $seller->id,
                'category_id' => 1,
                'title' => 'Áo khoác Khoa CNTT TDC',
                'description' => 'Áo khoác sinh viên TDC, màu xanh đen, vải thun lạnh thoáng mát.',
                'price' => 120000,
                'condition' => 'new',
                'status' => 'approved',
                'location' => 'Dĩ An, Bình Dương',
                'views_count' => 98,
                'approved_at' => now(),
                'approved_by' => User::where('role', 'admin')->value('id') ?? 1,
            ],
        ];

        foreach ($approvedSamples as $sample) {
            Listing::create($sample);
        }

        // Pending samples
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
            'rejected_by' => User::where('role', 'admin')->value('id') ?? 1,
            'rejection_reason' => 'policy_violation',
        ]);
    }
}
