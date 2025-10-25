<?php

namespace App\Console\Commands;

use App\Models\Listing;
use App\Models\User;
use App\Models\Category;
use App\Models\ListingImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class SimpleDataGenerator extends Command
{
    protected $signature = 'generate:simple-data {count=100}';
    protected $description = 'Generate simple random data without complex operations';

    public function handle()
    {
        $count = (int) $this->argument('count');
        $faker = Faker::create('vi_VN');
        
        $this->info("🚀 Generating {$count} simple listings...");
        
        // Get or create basic data
        $users = $this->getOrCreateUsers();
        $categories = $this->getOrCreateCategories();
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            try {
                // Create listing
                $listing = Listing::create([
                    'seller_id' => $users->random()->id,
                    'category_id' => $categories->random()->id,
                    'title' => $this->generateSimpleTitle($faker),
                    'description' => $this->generateSimpleDescription($faker),
                    'condition' => $faker->randomElement(['new', 'like_new', 'good', 'fair']),
                    'price' => $faker->numberBetween(10000, 50000000),
                    'status' => 'approved',
                    'location' => $faker->city(),
                    'views_count' => $faker->numberBetween(0, 1000),
                    'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                ]);

                // Create simple image record (without actual file)
                $this->createSimpleImage($listing, $faker);
                
            } catch (\Exception $e) {
                $this->error("Error creating listing {$i}: " . $e->getMessage());
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Successfully generated {$count} listings!");
    }

    private function getOrCreateUsers()
    {
        $users = User::where('role', 'user')->get();
        
        if ($users->isEmpty()) {
            $this->info('Creating sample users...');
            for ($i = 0; $i < 10; $i++) {
                User::create([
                    'name' => 'User ' . ($i + 1),
                    'email' => 'user' . ($i + 1) . '@example.com',
                    'phone' => '012345678' . $i,
                    'password' => Hash::make('password123'),
                    'role' => 'user',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);
            }
            $users = User::where('role', 'user')->get();
        }
        
        return $users;
    }

    private function getOrCreateCategories()
    {
        $categories = Category::all();
        
        if ($categories->isEmpty()) {
            $this->info('Creating sample categories...');
            $categoryNames = [
                'Sách giáo khoa', 'Điện tử', 'Quần áo', 'Đồ chơi', 
                'Nhạc cụ', 'Thể thao', 'Mỹ phẩm', 'Thực phẩm'
            ];
            
            foreach ($categoryNames as $name) {
                Category::create([
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'description' => 'Danh mục ' . $name,
                    'icon' => 'default',
                    'is_active' => true,
                ]);
            }
            $categories = Category::all();
        }
        
        return $categories;
    }

    private function generateSimpleTitle($faker)
    {
        $prefixes = ['Bán', 'Cần bán', 'Thanh lý', 'Bán gấp'];
        $items = ['Laptop', 'Điện thoại', 'Sách', 'Quần áo', 'Giày dép', 'Đồ chơi'];
        $adjectives = ['cũ', 'mới', 'đẹp', 'tốt', 'rẻ', 'cao cấp'];
        
        return $faker->randomElement($prefixes) . ' ' . 
               $faker->randomElement($items) . ' ' . 
               $faker->randomElement($adjectives);
    }

    private function generateSimpleDescription($faker)
    {
        $templates = [
            'Sản phẩm chất lượng tốt, còn mới 90%. Phù hợp cho sinh viên.',
            'Đã sử dụng ít, tình trạng rất tốt. Bán vì không còn nhu cầu.',
            'Sản phẩm chính hãng, có hóa đơn mua hàng. Bảo hành còn lại.',
            'Mua về dùng thử, không phù hợp nên bán lại. Giá rẻ hơn thị trường.',
        ];
        
        return $faker->randomElement($templates);
    }

    private function createSimpleImage($listing, $faker)
    {
        ListingImage::create([
            'listing_id' => $listing->id,
            'image_path' => 'listings/placeholder.jpg',
            'original_name' => 'sample_' . $faker->word() . '.jpg',
            'file_size' => $faker->numberBetween(50000, 500000),
            'mime_type' => 'image/jpeg',
            'width' => $faker->numberBetween(300, 800),
            'height' => $faker->numberBetween(300, 600),
            'is_primary' => true,
            'sort_order' => 0,
        ]);
    }
}
