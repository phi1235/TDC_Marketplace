<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Category;
use App\Models\Listing;
use App\Models\Wishlist;
use App\Models\Offer;
use App\Models\Review;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class GenerateRandomData extends Command
{
    protected $signature = 'generate:random-data {--users=100} {--categories=20} {--listings=1000} {--wishlists=500} {--offers=300} {--reviews=200}';
    protected $description = 'Generate comprehensive random data for the marketplace';

    public function handle()
    {
        $faker = Faker::create('vi_VN');
        
        $this->info('🚀 Starting random data generation...');
        
        // Generate users
        $this->generateUsers($faker, $this->option('users'));
        
        // Generate categories
        $this->generateCategories($faker, $this->option('categories'));
        
        // Generate listings
        $this->generateListings($faker, $this->option('listings'));
        
        // Generate wishlists
        $this->generateWishlists($faker, $this->option('wishlists'));
        
        // Generate offers
        $this->generateOffers($faker, $this->option('offers'));
        
        // Generate reviews
        $this->generateReviews($faker, $this->option('reviews'));
        
        $this->info('✅ Random data generation completed!');
    }

    private function generateUsers($faker, $count)
    {
        $this->info("👥 Generating {$count} users...");
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            User::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'phone' => $faker->phoneNumber(),
                'password' => Hash::make('password123'),
                'role' => $faker->randomElement(['user', 'admin']),
                'is_active' => $faker->boolean(90), // 90% active
                'email_verified_at' => $faker->optional(0.8)->dateTimeBetween('-1 year', 'now'),
                'created_at' => $faker->dateTimeBetween('-2 years', 'now'),
            ]);
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
    }

    private function generateCategories($faker, $count)
    {
        $this->info("📂 Generating {$count} categories...");
        
        $categories = [
            'Sách giáo khoa', 'Sách tham khảo', 'Tài liệu học tập', 'Đồ dùng học tập',
            'Điện tử', 'Điện thoại', 'Laptop', 'Máy tính', 'Phụ kiện điện tử',
            'Quần áo', 'Giày dép', 'Túi xách', 'Phụ kiện thời trang',
            'Đồ chơi', 'Game', 'Board game', 'Card game', 'Puzzle',
            'Nhạc cụ', 'Guitar', 'Piano', 'Trống', 'Đàn organ',
            'Thể thao', 'Bóng đá', 'Bóng rổ', 'Cầu lông', 'Tennis',
            'Mỹ phẩm', 'Nước hoa', 'Son môi', 'Kem dưỡng', 'Serum',
            'Thực phẩm', 'Đồ uống', 'Trái cây', 'Rau củ', 'Hải sản',
            'Nội thất', 'Bàn ghế', 'Tủ', 'Giường', 'Sofa',
            'Xe cộ', 'Xe đạp', 'Xe máy', 'Phụ kiện xe', 'Đồ chơi xe'
        ];

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            $name = $faker->randomElement($categories);
            Category::create([
                'name' => $name,
                'slug' => \Str::slug($name),
                'description' => $faker->sentence(10),
                'icon' => $faker->randomElement(['book', 'laptop', 'shirt', 'game', 'music', 'sport', 'beauty', 'food', 'furniture', 'car']),
                'is_active' => $faker->boolean(95),
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
            ]);
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
    }

    private function generateListings($faker, $count)
    {
        $this->info("📝 Generating {$count} listings...");
        
        $users = User::where('role', 'user')->get();
        $categories = Category::all();
        
        if ($users->isEmpty() || $categories->isEmpty()) {
            $this->error('❌ No users or categories found. Please run user and category generation first.');
            return;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            $listing = Listing::create([
                'seller_id' => $users->random()->id,
                'category_id' => $categories->random()->id,
                'title' => $this->generateRandomTitle($faker),
                'description' => $this->generateRandomDescription($faker),
                'condition' => $faker->randomElement(['new', 'like_new', 'good', 'fair']),
                'price' => $faker->numberBetween(10000, 100000000), // 10k - 100M VND
                'status' => $faker->randomElement(['approved', 'pending', 'rejected']),
                'location' => $faker->city(),
                'views_count' => $faker->numberBetween(0, 5000),
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'updated_at' => now(),
            ]);

            // Add random images
            $imageCount = $faker->numberBetween(1, 5);
            for ($j = 0; $j < $imageCount; $j++) {
                $this->createRandomImage($listing, $faker);
            }

            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
    }

    private function generateWishlists($faker, $count)
    {
        $this->info("❤️ Generating {$count} wishlists...");
        
        $users = User::where('role', 'user')->get();
        $listings = Listing::where('status', 'approved')->get();
        
        if ($users->isEmpty() || $listings->isEmpty()) {
            $this->error('❌ No users or listings found.');
            return;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            Wishlist::create([
                'user_id' => $users->random()->id,
                'listing_id' => $listings->random()->id,
                'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
            ]);
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
    }

    private function generateOffers($faker, $count)
    {
        $this->info("💰 Generating {$count} offers...");
        
        $users = User::where('role', 'user')->get();
        $listings = Listing::where('status', 'approved')->get();
        
        if ($users->isEmpty() || $listings->isEmpty()) {
            $this->error('❌ No users or listings found.');
            return;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            $listing = $listings->random();
            $offerPrice = $faker->numberBetween($listing->price * 0.5, $listing->price * 1.2);
            
            Offer::create([
                'listing_id' => $listing->id,
                'buyer_id' => $users->random()->id,
                'amount' => $offerPrice,
                'message' => $faker->optional(0.7)->sentence(10),
                'status' => $faker->randomElement(['pending', 'accepted', 'rejected']),
                'created_at' => $faker->dateTimeBetween('-3 months', 'now'),
            ]);
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
    }

    private function generateReviews($faker, $count)
    {
        $this->info("⭐ Generating {$count} reviews...");
        
        $users = User::where('role', 'user')->get();
        $listings = Listing::where('status', 'approved')->get();
        
        if ($users->isEmpty() || $listings->isEmpty()) {
            $this->error('❌ No users or listings found.');
            return;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            Review::create([
                'listing_id' => $listings->random()->id,
                'user_id' => $users->random()->id,
                'rating' => $faker->numberBetween(1, 5),
                'comment' => $faker->optional(0.8)->sentence(15),
                'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
            ]);
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
    }

    private function generateRandomTitle($faker)
    {
        $prefixes = ['Bán', 'Cần bán', 'Thanh lý', 'Bán gấp', 'Bán rẻ', 'Tặng kèm'];
        $items = ['Laptop', 'Điện thoại', 'Sách', 'Quần áo', 'Giày dép', 'Đồ chơi', 'Nhạc cụ', 'Thể thao'];
        $adjectives = ['cũ', 'mới', 'đẹp', 'tốt', 'rẻ', 'cao cấp', 'hiếm', 'độc đáo'];
        
        return $faker->randomElement($prefixes) . ' ' . 
               $faker->randomElement($items) . ' ' . 
               $faker->randomElement($adjectives);
    }

    private function generateRandomDescription($faker)
    {
        $templates = [
            'Sản phẩm chất lượng tốt, còn mới 90%. Phù hợp cho sinh viên.',
            'Đã sử dụng ít, tình trạng rất tốt. Bán vì không còn nhu cầu.',
            'Sản phẩm chính hãng, có hóa đơn mua hàng. Bảo hành còn lại.',
            'Mua về dùng thử, không phù hợp nên bán lại. Giá rẻ hơn thị trường.',
            'Đồ cũ nhưng còn sử dụng tốt. Phù hợp cho người có thu nhập thấp.',
        ];
        
        return $faker->randomElement($templates) . ' ' . $faker->sentence(5);
    }

    private function createRandomImage($listing, $faker)
    {
        // Simple placeholder image generation
        $width = $faker->numberBetween(300, 800);
        $height = $faker->numberBetween(300, 600);
        
        $image = imagecreate($width, $height);
        $bgColor = imagecolorallocate($image, 
            $faker->numberBetween(100, 255),
            $faker->numberBetween(100, 255), 
            $faker->numberBetween(100, 255)
        );
        
        $filename = 'listings/' . date('Y/m/d') . '/' . \Str::random(40) . '.jpg';
        $fullPath = storage_path('app/public/' . $filename);
        
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        imagejpeg($image, $fullPath, 80);
        imagedestroy($image);
        
        \App\Models\ListingImage::create([
            'listing_id' => $listing->id,
            'image_path' => $filename,
            'original_name' => 'sample_' . $faker->word() . '.jpg',
            'file_size' => filesize($fullPath),
            'mime_type' => 'image/jpeg',
            'width' => $width,
            'height' => $height,
            'is_primary' => $listing->images()->count() === 0,
        ]);
    }
}
