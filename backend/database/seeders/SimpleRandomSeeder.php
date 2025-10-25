<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Listing;
use App\Models\User;
use App\Models\Category;
use App\Models\ListingImage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SimpleRandomSeeder extends Seeder
{
    public function run()
    {
        $count = 1000; // Generate 1000 random listings
        
        echo "🚀 Generating {$count} random listings...\n";
        
        // Create users if needed
        if (User::where('role', 'user')->count() < 10) {
            echo "👥 Creating sample users...\n";
            for ($i = 0; $i < 20; $i++) {
                User::create([
                    'name' => 'User ' . ($i + 1),
                    'email' => 'user' . ($i + 1) . '@tdc.edu.vn',
                    'phone' => '012345678' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'password' => Hash::make('password123'),
                    'role' => 'user',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);
            }
        }
        
        // Create categories if needed
        if (Category::count() < 5) {
            echo "📂 Creating sample categories...\n";
            $categories = [
                ['name' => 'Sách giáo khoa', 'slug' => 'sach-giao-khoa', 'icon' => 'book'],
                ['name' => 'Điện tử', 'slug' => 'dien-tu', 'icon' => 'laptop'],
                ['name' => 'Quần áo', 'slug' => 'quan-ao', 'icon' => 'shirt'],
                ['name' => 'Đồ chơi', 'slug' => 'do-choi', 'icon' => 'game'],
                ['name' => 'Nhạc cụ', 'slug' => 'nhac-cu', 'icon' => 'music'],
                ['name' => 'Thể thao', 'slug' => 'the-thao', 'icon' => 'sport'],
                ['name' => 'Mỹ phẩm', 'slug' => 'my-pham', 'icon' => 'beauty'],
                ['name' => 'Thực phẩm', 'slug' => 'thuc-pham', 'icon' => 'food'],
                ['name' => 'Nội thất', 'slug' => 'noi-that', 'icon' => 'furniture'],
                ['name' => 'Xe cộ', 'slug' => 'xe-co', 'icon' => 'car'],
            ];
            
            foreach ($categories as $category) {
                Category::create([
                    'name' => $category['name'],
                    'slug' => $category['slug'],
                    'description' => 'Danh mục ' . $category['name'],
                    'icon' => $category['icon'],
                    'is_active' => true,
                ]);
            }
        }
        
        $users = User::where('role', 'user')->get();
        $categories = Category::all();
        
        if ($users->isEmpty() || $categories->isEmpty()) {
            echo "❌ No users or categories found!\n";
            return;
        }
        
        // Generate random listings using raw SQL for better performance
        $this->generateListingsWithSQL($count, $users, $categories);
        
        echo "✅ Successfully generated {$count} random listings!\n";
    }
    
    private function generateListingsWithSQL($count, $users, $categories)
    {
        $userIds = $users->pluck('id')->toArray();
        $categoryIds = $categories->pluck('id')->toArray();
        
        $prefixes = ['Bán', 'Cần bán', 'Thanh lý', 'Bán gấp', 'Bán rẻ', 'Tặng kèm'];
        $items = ['Laptop', 'Điện thoại', 'Sách', 'Quần áo', 'Giày dép', 'Đồ chơi', 'Nhạc cụ', 'Thể thao', 'Mỹ phẩm', 'Thực phẩm'];
        $adjectives = ['cũ', 'mới', 'đẹp', 'tốt', 'rẻ', 'cao cấp', 'hiếm', 'độc đáo'];
        $conditions = ['new', 'like_new', 'good', 'fair'];
        $statuses = ['approved', 'pending', 'rejected'];
        $locations = ['Hà Nội', 'TP.HCM', 'Đà Nẵng', 'Cần Thơ', 'Hải Phòng', 'Nha Trang', 'Huế', 'Vũng Tàu'];
        
        $descriptions = [
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
        ];
        
        // Generate listings in batches
        $batchSize = 100;
        $batches = ceil($count / $batchSize);
        
        for ($batch = 0; $batch < $batches; $batch++) {
            $currentBatchSize = min($batchSize, $count - ($batch * $batchSize));
            $values = [];
            
            for ($i = 0; $i < $currentBatchSize; $i++) {
                $prefix = $prefixes[array_rand($prefixes)];
                $item = $items[array_rand($items)];
                $adjective = $adjectives[array_rand($adjectives)];
                $title = $prefix . ' ' . $item . ' ' . $adjective;
                
                $description = $descriptions[array_rand($descriptions)];
                $condition = $conditions[array_rand($conditions)];
                $status = $statuses[array_rand($statuses)];
                $location = $locations[array_rand($locations)];
                $price = rand(10000, 50000000);
                $viewsCount = rand(0, 1000);
                $sellerId = $userIds[array_rand($userIds)];
                $categoryId = $categoryIds[array_rand($categoryIds)];
                $createdAt = date('Y-m-d H:i:s', rand(strtotime('-6 months'), time()));
                
                $values[] = "({$sellerId}, {$categoryId}, '{$title}', '{$description}', '{$condition}', {$price}, '{$status}', '{$location}', {$viewsCount}, '{$createdAt}', NOW())";
            }
            
            if (!empty($values)) {
                $sql = "INSERT INTO listings (seller_id, category_id, title, description, condition, price, status, location, views_count, created_at, updated_at) VALUES " . implode(',', $values);
                DB::statement($sql);
                
                // Get the inserted listing IDs and create images
                $listingIds = DB::select("SELECT id FROM listings ORDER BY id DESC LIMIT {$currentBatchSize}");
                
                foreach ($listingIds as $listing) {
                    $imageCount = rand(1, 3);
                    for ($j = 0; $j < $imageCount; $j++) {
                        $imagePath = 'listings/' . date('Y/m/d') . '/' . uniqid() . '.jpg';
                        $originalName = 'sample_' . uniqid() . '.jpg';
                        $fileSize = rand(50000, 500000);
                        $width = rand(300, 800);
                        $height = rand(300, 600);
                        $isPrimary = $j === 0;
                        
                        DB::table('listing_images')->insert([
                            'listing_id' => $listing->id,
                            'image_path' => $imagePath,
                            'original_name' => $originalName,
                            'file_size' => $fileSize,
                            'mime_type' => 'image/jpeg',
                            'width' => $width,
                            'height' => $height,
                            'is_primary' => $isPrimary,
                            'sort_order' => $j,
                            'created_at' => $createdAt,
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
            
            echo "✅ Generated batch " . ($batch + 1) . "/{$batches}\n";
        }
    }
}
