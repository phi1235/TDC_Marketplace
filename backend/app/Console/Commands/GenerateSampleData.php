<?php

namespace App\Console\Commands;

use App\Models\Listing;
use App\Models\User;
use App\Models\Category;
use App\Models\ListingImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class GenerateSampleData extends Command
{
    protected $signature = 'generate:sample-data {count=1000}';
    protected $description = 'Generate random sample data for listings';

    public function handle()
    {
        $count = (int) $this->argument('count');
        $faker = Faker::create('vi_VN'); // Vietnamese locale
        
        $this->info("Generating {$count} sample listings...");
        
        // Get existing users and categories
        $users = User::where('role', 'user')->get();
        $categories = Category::all();
        
        if ($users->isEmpty()) {
            $this->error('No users found. Please create some users first.');
            return;
        }
        
        if ($categories->isEmpty()) {
            $this->error('No categories found. Please create some categories first.');
            return;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            // Create listing
            $listing = Listing::create([
                'seller_id' => $users->random()->id,
                'category_id' => $categories->random()->id,
                'title' => $this->generateTitle($faker),
                'description' => $this->generateDescription($faker),
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
                $this->createRandomImage($listing, $faker);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully generated {$count} sample listings!");
    }

    private function generateTitle($faker)
    {
        $prefixes = [
            'Bán', 'Cần bán', 'Thanh lý', 'Bán gấp', 'Bán rẻ', 'Bán mới',
            'Tặng kèm', 'Bán kèm', 'Bán combo', 'Bán set'
        ];
        
        $items = [
            'Laptop', 'Điện thoại', 'Máy tính', 'Sách', 'Quần áo', 'Giày dép',
            'Túi xách', 'Đồng hồ', 'Kính mắt', 'Bàn ghế', 'Tủ lạnh', 'Máy giặt',
            'TV', 'Loa', 'Tai nghe', 'Bàn phím', 'Chuột', 'Webcam', 'Microphone',
            'Guitar', 'Piano', 'Saxophone', 'Trống', 'Đàn organ', 'Violin',
            'Xe đạp', 'Xe máy', 'Ô tô', 'Mô tô', 'Tàu thuyền', 'Máy bay',
            'Đồ chơi', 'Game', 'Board game', 'Card game', 'Puzzle', 'Lego',
            'Mỹ phẩm', 'Nước hoa', 'Son môi', 'Kem dưỡng', 'Serum', 'Toner',
            'Thực phẩm', 'Đồ uống', 'Trái cây', 'Rau củ', 'Thịt cá', 'Hải sản'
        ];
        
        $adjectives = [
            'cũ', 'mới', 'đẹp', 'xấu', 'tốt', 'tệ', 'rẻ', 'đắt', 'cao cấp', 'bình dân',
            'hiếm', 'phổ biến', 'độc đáo', 'đặc biệt', 'thú vị', 'hấp dẫn', 'bắt mắt',
            'chất lượng', 'bền', 'nhẹ', 'nặng', 'lớn', 'nhỏ', 'dài', 'ngắn', 'rộng', 'hẹp'
        ];
        
        $prefix = $faker->randomElement($prefixes);
        $item = $faker->randomElement($items);
        $adjective = $faker->randomElement($adjectives);
        
        return "{$prefix} {$item} {$adjective}";
    }

    private function generateDescription($faker)
    {
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
        
        $baseDescription = $faker->randomElement($descriptions);
        
        // Add some random details
        $details = [
            'Liên hệ: ' . $faker->phoneNumber(),
            'Địa chỉ: ' . $faker->address(),
            'Thời gian: ' . $faker->time('H:i'),
            'Giao hàng: ' . ($faker->boolean() ? 'Có' : 'Không'),
            'Đổi trả: ' . ($faker->boolean() ? 'Có' : 'Không'),
        ];
        
        $randomDetails = $faker->randomElements($details, $faker->numberBetween(1, 3));
        
        return $baseDescription . "\n\n" . implode("\n", $randomDetails);
    }

    private function createRandomImage($listing, $faker)
    {
        // Create a simple placeholder image using GD
        $width = $faker->numberBetween(300, 800);
        $height = $faker->numberBetween(300, 800);
        
        $image = imagecreate($width, $height);
        
        // Random background color
        $bgColor = imagecolorallocate($image, 
            $faker->numberBetween(100, 255),
            $faker->numberBetween(100, 255), 
            $faker->numberBetween(100, 255)
        );
        
        // Add some random shapes
        $shapeColor = imagecolorallocate($image, 
            $faker->numberBetween(0, 100),
            $faker->numberBetween(0, 100), 
            $faker->numberBetween(0, 100)
        );
        
        // Draw random rectangle
        imagefilledrectangle($image, 
            $faker->numberBetween(0, $width/2), 
            $faker->numberBetween(0, $height/2),
            $faker->numberBetween($width/2, $width),
            $faker->numberBetween($height/2, $height),
            $shapeColor
        );
        
        // Save image
        $filename = 'listings/' . date('Y/m/d') . '/' . Str::random(40) . '.jpg';
        $fullPath = storage_path('app/public/' . $filename);
        
        // Create directory if not exists
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        imagejpeg($image, $fullPath, 80);
        imagedestroy($image);
        
        // Create database record
        ListingImage::create([
            'listing_id' => $listing->id,
            'image_path' => $filename,
            'original_name' => 'sample_image_' . $faker->word() . '.jpg',
            'file_size' => filesize($fullPath),
            'mime_type' => 'image/jpeg',
            'width' => $width,
            'height' => $height,
            'is_primary' => $listing->images()->count() === 0,
            'sort_order' => $listing->images()->count(),
        ]);
    }
}
