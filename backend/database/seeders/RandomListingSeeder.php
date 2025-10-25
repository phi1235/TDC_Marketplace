<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Listing;
use App\Models\User;
use App\Models\Category;
use App\Models\ListingImage;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class RandomListingSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('vi_VN');
        $count = 1500; // Generate 1500 random listings
        
        $this->command->info("🚀 Generating {$count} random listings...");
        
        // Ensure we have users and categories
        $this->createUsersIfNeeded();
        $this->createCategoriesIfNeeded();
        
        $users = User::where('role', 'user')->get();
        $categories = Category::all();
        
        if ($users->isEmpty() || $categories->isEmpty()) {
            $this->command->error('❌ No users or categories found!');
            return;
        }
        
        $bar = $this->command->getOutput()->createProgressBar($count);
        $bar->start();
        
        for ($i = 0; $i < $count; $i++) {
            // Create random listing
            $listing = Listing::create([
                'seller_id' => $users->random()->id,
                'category_id' => $categories->random()->id,
                'title' => $this->generateRandomTitle($faker),
                'description' => $this->generateRandomDescription($faker),
                'condition' => $faker->randomElement(['new', 'like_new', 'good', 'fair']),
                'price' => $faker->numberBetween(10000, 50000000), // 10k - 50M VND
                'status' => $faker->randomElement(['approved', 'pending', 'rejected']),
                'location' => $faker->city(),
                'views_count' => $faker->numberBetween(0, 1000),
                'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                'updated_at' => now(),
            ]);
            
            // Create 1-3 random images for each listing
            $imageCount = $faker->numberBetween(1, 3);
            for ($j = 0; $j < $imageCount; $j++) {
                $this->createRandomImage($listing, $faker, $j);
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->command->newLine();
        $this->command->info("✅ Successfully generated {$count} random listings!");
    }
    
    private function createUsersIfNeeded()
    {
        if (User::where('role', 'user')->count() < 10) {
            $this->command->info('👥 Creating sample users...');
            
            for ($i = 0; $i < 20; $i++) {
                User::create([
                    'name' => 'User ' . ($i + 1),
                    'email' => 'user' . ($i + 1) . '@tdc.edu.vn',
                    'phone' => '012345678' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'password' => Hash::make('password123'),
                    'role' => 'user',
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
    
    private function createCategoriesIfNeeded()
    {
        if (Category::count() < 5) {
            $this->command->info('📂 Creating sample categories...');
            
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
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
    
    private function generateRandomTitle($faker)
    {
        $prefixes = ['Bán', 'Cần bán', 'Thanh lý', 'Bán gấp', 'Bán rẻ', 'Tặng kèm', 'Bán combo', 'Bán set'];
        $items = [
            'Laptop', 'Điện thoại', 'Máy tính', 'Sách', 'Quần áo', 'Giày dép', 'Túi xách', 'Đồng hồ',
            'Kính mắt', 'Bàn ghế', 'Tủ lạnh', 'Máy giặt', 'TV', 'Loa', 'Tai nghe', 'Bàn phím',
            'Chuột', 'Webcam', 'Microphone', 'Guitar', 'Piano', 'Saxophone', 'Trống', 'Đàn organ',
            'Violin', 'Xe đạp', 'Xe máy', 'Ô tô', 'Mô tô', 'Tàu thuyền', 'Máy bay', 'Đồ chơi',
            'Game', 'Board game', 'Card game', 'Puzzle', 'Lego', 'Mỹ phẩm', 'Nước hoa', 'Son môi',
            'Kem dưỡng', 'Serum', 'Toner', 'Thực phẩm', 'Đồ uống', 'Trái cây', 'Rau củ', 'Thịt cá',
            'Hải sản', 'Nhạc cụ', 'Dụng cụ thể thao', 'Quần áo thể thao', 'Giày thể thao', 'Bóng đá',
            'Bóng rổ', 'Cầu lông', 'Tennis', 'Bơi lội', 'Chạy bộ', 'Gym', 'Yoga', 'Pilates'
        ];
        $adjectives = [
            'cũ', 'mới', 'đẹp', 'xấu', 'tốt', 'tệ', 'rẻ', 'đắt', 'cao cấp', 'bình dân',
            'hiếm', 'phổ biến', 'độc đáo', 'đặc biệt', 'thú vị', 'hấp dẫn', 'bắt mắt',
            'chất lượng', 'bền', 'nhẹ', 'nặng', 'lớn', 'nhỏ', 'dài', 'ngắn', 'rộng', 'hẹp',
            'màu đỏ', 'màu xanh', 'màu vàng', 'màu đen', 'màu trắng', 'màu hồng', 'màu tím',
            'vintage', 'retro', 'modern', 'cổ điển', 'hiện đại', 'truyền thống', 'phong cách'
        ];
        
        return $faker->randomElement($prefixes) . ' ' . 
               $faker->randomElement($items) . ' ' . 
               $faker->randomElement($adjectives);
    }
    
    private function generateRandomDescription($faker)
    {
        $templates = [
            'Sản phẩm chất lượng tốt, còn mới 90%. Phù hợp cho sinh viên, người đi làm. Liên hệ: {phone}',
            'Đã sử dụng ít, tình trạng rất tốt. Bán vì không còn nhu cầu sử dụng. Giá rẻ hơn thị trường.',
            'Sản phẩm chính hãng, có hóa đơn mua hàng. Bảo hành còn lại 6 tháng. Địa chỉ: {address}',
            'Mua về dùng thử, không phù hợp nên bán lại. Giá rẻ, phù hợp cho người có thu nhập thấp.',
            'Đồ cũ nhưng còn sử dụng tốt. Phù hợp cho người có thu nhập thấp. Giao hàng tận nơi.',
            'Sản phẩm cao cấp, ít sử dụng. Bán vì chuyển nhà, không mang theo được. Thời gian: {time}',
            'Hàng thanh lý, giá rẻ. Còn sử dụng được, phù hợp cho sinh viên. Đổi trả: Có',
            'Đồ cũ nhưng chất lượng tốt. Bán vì không còn nhu cầu sử dụng. Giao hàng: Có',
            'Sản phẩm mới 100%, chưa sử dụng. Có hóa đơn mua hàng đầy đủ. Bảo hành: 12 tháng',
            'Đồ cũ nhưng còn sử dụng tốt. Giá rẻ, phù hợp cho người có thu nhập thấp. Liên hệ sớm!'
        ];
        
        $template = $faker->randomElement($templates);
        
        // Replace placeholders
        $template = str_replace('{phone}', $faker->phoneNumber(), $template);
        $template = str_replace('{address}', $faker->address(), $template);
        $template = str_replace('{time}', $faker->time('H:i'), $template);
        
        return $template;
    }
    
    private function createRandomImage($listing, $faker, $index)
    {
        // Create simple image record without actual file
        ListingImage::create([
            'listing_id' => $listing->id,
            'image_path' => 'listings/' . date('Y/m/d') . '/' . uniqid() . '.jpg',
            'original_name' => 'sample_' . $faker->word() . '_' . ($index + 1) . '.jpg',
            'file_size' => $faker->numberBetween(50000, 500000),
            'mime_type' => 'image/jpeg',
            'width' => $faker->numberBetween(300, 800),
            'height' => $faker->numberBetween(300, 600),
            'is_primary' => $index === 0,
            'sort_order' => $index,
            'created_at' => $listing->created_at,
            'updated_at' => $listing->updated_at,
        ]);
    }
}
