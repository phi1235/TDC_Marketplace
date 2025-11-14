<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Listing;
use App\Models\User;
use App\Models\Major;
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

        // Get all majors for random assignment
        $majorIds = Major::pluck('id')->toArray();

        // Tạo dữ liệu mẫu với Eloquent
        $seller = User::where('role', 'user')->first() ?? User::first();
        if (!$seller) { 
            return; 
        }

        // Approved samples - Đa dạng category
        $approvedSamples = [
            // Category 1: Sách giáo khoa
            [
                'seller_id' => $seller->id,
                'category_id' => 1,
                'title' => 'Sách Giáo Khoa Toán 12',
                'description' => 'Bộ sách giáo khoa Toán lớp 12 – bản chuẩn của Bộ GD&ĐT. Còn mới 95%, không ghi chép.',
                'condition' => 'like_new',
                'price' => 45000,
                'status' => 'approved',
                'location' => 'Thủ Đức, TP.HCM',
                'views_count' => 125,
                'approved_at' => now(),
                'approved_by' => User::where('role', 'admin')->value('id') ?? 1,
                'pickup_point_id' => 1,
            ],
            [
                'seller_id' => $seller->id,
                'category_id' => 1,
                'title' => 'Combo Sách Tiếng Anh A2-B1',
                'description' => 'Bộ sách Market Leader Elementary + Intermediate, kèm CD nghe. Đã học xong, còn đẹp.',
                'condition' => 'good',
                'price' => 120000,
                'status' => 'approved',
                'location' => 'TDC Campus, Khu A',
                'views_count' => 89,
                'approved_at' => now(),
                'approved_by' => User::where('role', 'admin')->value('id') ?? 1,
                'pickup_point_id' => 2,
            ],
            
            // Category 2: Sách tham khảo
            [
                'seller_id' => $seller->id,
                'category_id' => 2,
                'title' => 'Giáo trình Java Programming',
                'description' => 'Giáo trình lập trình Java cho sinh viên CNTT. Có code mẫu và bài tập.',
                'condition' => 'good',
                'price' => 85000,
                'status' => 'approved',
                'location' => 'Linh Trung, Thủ Đức',
                'views_count' => 156,
                'approved_at' => now(),
                'approved_by' => User::where('role', 'admin')->value('id') ?? 1,
                'pickup_point_id' => 1,
            ],
            [
                'seller_id' => $seller->id,
                'category_id' => 2,
                'title' => 'Head First Design Patterns',
                'description' => 'Sách kinh điển về design patterns. Bản tiếng Anh, còn mới keng.',
                'condition' => 'like_new',
                'price' => 200000,
                'status' => 'approved',
                'location' => 'Khu B, TDC',
                'views_count' => 203,
                'approved_at' => now(),
                'approved_by' => User::where('role', 'admin')->value('id') ?? 1,
                'pickup_point_id' => 3,
            ],

            // Category 3: Đồ dùng học tập
            [
                'seller_id' => $seller->id,
                'category_id' => 3,
                'title' => 'Bút bi Thiên Long TL-027 (hộp 50 cây)',
                'description' => 'Hộp bút bi Thiên Long TL-027 màu xanh, mực đậm, viết êm. Còn nguyên seal.',
                'price' => 80000,
                'condition' => 'new',
                'status' => 'approved',
                'location' => 'Thủ Đức, TP.HCM',
                'views_count' => 78,
                'approved_at' => now(),
                'approved_by' => User::where('role', 'admin')->value('id') ?? 1,
                'pickup_point_id' => 4,
            ],
            [
                'seller_id' => $seller->id,
                'category_id' => 3,
                'title' => 'Máy tính Casio FX-570VN PLUS',
                'description' => 'Máy tính khoa học Casio, dùng được cho thi đại học. Mua 2 tháng trước, còn bảo hành.',
                'price' => 350000,
                'condition' => 'like_new',
                'status' => 'approved',
                'location' => 'Phường Linh Chiểu',
                'views_count' => 234,
                'approved_at' => now(),
                'approved_by' => User::where('role', 'admin')->value('id') ?? 1,
                'pickup_point_id' => 1,
            ],

            // Category 4: Điện tử
            [
                'seller_id' => $seller->id,
                'category_id' => 4,
                'title' => 'Laptop Dell Inspiron 15 3000',
                'description' => 'Laptop Dell cũ phù hợp cho sinh viên học CNTT. Core i5 Gen 8, RAM 8GB, SSD 256GB, màn 15.6 inch. Máy chạy mượt, pin 4-5h.',
                'condition' => 'good',
                'price' => 6500000,
                'status' => 'approved',
                'location' => 'Quận 9, TP.HCM',
                'views_count' => 432,
                'approved_at' => now(),
                'approved_by' => User::where('role', 'admin')->value('id') ?? 1,
                'pickup_point_id' => 2,
            ],
            [
                'seller_id' => $seller->id,
                'category_id' => 4,
                'title' => 'Tai nghe Bluetooth Sony WH-1000XM3',
                'description' => 'Tai nghe chống ồn Sony WH-1000XM3. Âm thanh đỉnh, pin 30h, kết nối ổn định. Còn nguyên hộp.',
                'condition' => 'like_new',
                'price' => 3200000,
                'status' => 'approved',
                'location' => 'TDC Campus',
                'views_count' => 189,
                'approved_at' => now(),
                'approved_by' => User::where('role', 'admin')->value('id') ?? 1,
                'pickup_point_id' => 5,
            ],
            [
                'seller_id' => $seller->id,
                'category_id' => 4,
                'title' => 'Chuột Logitech MX Master 3',
                'description' => 'Chuột không dây cao cấp Logitech MX Master 3. Rất tốt cho lập trình và thiết kế. Dùng 6 tháng.',
                'condition' => 'good',
                'price' => 1500000,
                'status' => 'approved',
                'location' => 'Khu A, TDC',
                'views_count' => 167,
                'approved_at' => now(),
                'approved_by' => User::where('role', 'admin')->value('id') ?? 1,
                'pickup_point_id' => 3,
            ],

            // Category 5: Quần áo
            [
                'seller_id' => $seller->id,
                'category_id' => 5,
                'title' => 'Áo khoác Khoa CNTT TDC',
                'description' => 'Áo khoác đồng phục sinh viên TDC màu xanh đen, vải thun lạnh thoáng mát. Size M, mới 100%.',
                'price' => 150000,
                'condition' => 'new',
                'status' => 'approved',
                'location' => 'Dĩ An, Bình Dương',
                'views_count' => 98,
                'approved_at' => now(),
                'approved_by' => User::where('role', 'admin')->value('id') ?? 1,
                'pickup_point_id' => 6,
            ],
            [
                'seller_id' => $seller->id,
                'category_id' => 5,
                'title' => 'Giày thể thao Nike Air Force 1',
                'description' => 'Giày Nike Air Force 1 màu trắng, size 42. Rep 1:1 chất lượng cao. Mang 3 lần.',
                'price' => 450000,
                'condition' => 'like_new',
                'status' => 'approved',
                'location' => 'Thủ Đức',
                'views_count' => 278,
                'approved_at' => now(),
                'approved_by' => User::where('role', 'admin')->value('id') ?? 1,
                'pickup_point_id' => 7,
            ],

            // Category 6: Đồ nội thất
            [
                'seller_id' => $seller->id,
                'category_id' => 6,
                'title' => 'Bàn học gỗ 1m2',
                'description' => 'Bàn học sinh viên bằng gỗ MDF, kích thước 1m x 60cm. Chắc chắn, còn đẹp.',
                'price' => 500000,
                'condition' => 'good',
                'status' => 'approved',
                'location' => 'Khu phố 6, Linh Trung',
                'views_count' => 123,
                'approved_at' => now(),
                'approved_by' => User::where('role', 'admin')->value('id') ?? 1,
                'pickup_point_id' => 1,
            ],
            [
                'seller_id' => $seller->id,
                'category_id' => 6,
                'title' => 'Ghế xoay văn phòng',
                'description' => 'Ghế xoay lưới thoáng mát, tay vịn điều chỉnh. Dùng 1 năm, không lỗi.',
                'price' => 650000,
                'condition' => 'good',
                'status' => 'approved',
                'location' => 'Gần TDC',
                'views_count' => 145,
                'approved_at' => now(),
                'approved_by' => User::where('role', 'admin')->value('id') ?? 1,
                'pickup_point_id' => 4,
            ],

            // Category 7: Thể thao
            [
                'seller_id' => $seller->id,
                'category_id' => 7,
                'title' => 'Bóng đá Molten FIFA Quality',
                'description' => 'Bóng đá Molten size 5, chuẩn FIFA Quality. Còn bơm căng, đá êm.',
                'price' => 280000,
                'condition' => 'good',
                'status' => 'approved',
                'location' => 'Sân bóng TDC',
                'views_count' => 92,
                'approved_at' => now(),
                'approved_by' => User::where('role', 'admin')->value('id') ?? 1,
                'pickup_point_id' => 5,
            ],
            [
                'seller_id' => $seller->id,
                'category_id' => 7,
                'title' => 'Bình nước thể thao Inox 1L',
                'description' => 'Bình nước giữ nhiệt inox 304, dung tích 1L. Giữ lạnh 12h, giữ nóng 6h.',
                'price' => 120000,
                'condition' => 'like_new',
                'status' => 'approved',
                'location' => 'Khu B',
                'views_count' => 67,
                'approved_at' => now(),
                'approved_by' => User::where('role', 'admin')->value('id') ?? 1,
                'pickup_point_id' => 4,
            ],

            // Category 8: Khác
            [
                'seller_id' => $seller->id,
                'category_id' => 8,
                'title' => 'Balo laptop The North Face',
                'description' => 'Balo laptop The North Face chống nước, ngăn laptop 15.6 inch. Dùng 6 tháng, còn đẹp.',
                'price' => 550000,
                'condition' => 'good',
                'status' => 'approved',
                'location' => 'TDC Campus',
                'views_count' => 187,
                'approved_at' => now(),
                'approved_by' => User::where('role', 'admin')->value('id') ?? 1,
                'pickup_point_id' => 2,
            ],
            [
                'seller_id' => $seller->id,
                'category_id' => 8,
                'title' => 'Đèn học LED chống cận',
                'description' => 'Đèn bàn học LED bảo vệ mắt, 3 chế độ ánh sáng, cổng USB sạc điện thoại.',
                'price' => 180000,
                'condition' => 'like_new',
                'status' => 'approved',
                'location' => 'Linh Chiểu',
                'views_count' => 134,
                'approved_at' => now(),
                'approved_by' => User::where('role', 'admin')->value('id') ?? 1,
                'pickup_point_id' => 8,
            ],
        ];

        foreach ($approvedSamples as $sample) {
            // Add random major_id
            $sample['major_id'] = !empty($majorIds) ? $majorIds[array_rand($majorIds)] : null;
            Listing::create($sample);
        }

        // Pending samples - Tin đang chờ duyệt
        $pendingSamples = [
            ['Giáo trình Toán Cao Cấp A2', 'Sách còn mới 90%, không ghi chép.', 120000, 2],
            ['Tai nghe Bluetooth Sony', 'Âm thanh tốt, pin bền 20h.', 450000, 4],
            ['Balo Adidas chống nước', 'Balo chống nước, ngăn laptop 15 inch.', 380000, 8],
            ['Máy tính Casio FX-880', 'Máy tính khoa học, còn bảo hành.', 280000, 3],
            ['Áo thun Uniqlo size M', 'Áo thun cotton 100%, mặc 2 lần.', 120000, 5],
            ['Giày Converse cổ cao', 'Giày Converse Chuck Taylor, size 41.', 550000, 5],
            ['Quạt mini USB', 'Quạt mini để bàn, USB sạc pin.', 65000, 8],
        ];

        foreach ($pendingSamples as [$title, $desc, $price, $catId]) {
            Listing::create([
                'seller_id' => $seller->id,
                'category_id' => $catId,
                'title' => $title,
                'description' => $desc,
                'price' => $price,
                'condition' => 'good',
                'status' => 'pending',
                'location' => 'TDC Campus, Khu A',
                'views_count' => 0,
                'major_id' => !empty($majorIds) ? $majorIds[array_rand($majorIds)] : null,
            ]);
        }

        // Rejected samples - Tin bị từ chối
        $rejectedSamples = [
            [
                'title' => 'iPhone 6s cũ',
                'description' => 'Máy cũ, pin yếu.',
                'price' => 900000,
                'category_id' => 4,
                'reason' => 'policy_violation',
                'note' => 'Không rõ nguồn gốc, thiếu thông tin chi tiết'
            ],
            [
                'title' => 'Giày Nike fake',
                'description' => 'Giày rep giá rẻ.',
                'price' => 200000,
                'category_id' => 5,
                'reason' => 'policy_violation',
                'note' => 'Hàng giả mạo thương hiệu, vi phạm chính sách'
            ],
        ];

        foreach ($rejectedSamples as $data) {
            Listing::create([
                'seller_id' => $seller->id,
                'category_id' => $data['category_id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'price' => $data['price'],
                'condition' => 'fair',
                'status' => 'rejected',
                'location' => 'TDC Campus',
                'views_count' => 0,
                'rejected_at' => now(),
                'rejected_by' => User::where('role', 'admin')->value('id') ?? 1,
                'rejection_reason' => $data['reason'],
                'admin_notes' => $data['note'],
                'major_id' => !empty($majorIds) ? $majorIds[array_rand($majorIds)] : null,
            ]);
        }
    }
}
