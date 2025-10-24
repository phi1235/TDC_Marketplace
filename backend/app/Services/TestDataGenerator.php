<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestDataGenerator
{
    protected $vietnameseProducts = [
        'sách giáo khoa', 'sách tham khảo', 'sách bài tập', 'sách nâng cao',
        'máy tính bảng', 'laptop cũ', 'máy tính để bàn', 'chuột máy tính',
        'bàn học', 'ghế học', 'tủ sách', 'đèn học',
        'xe đạp', 'xe máy', 'mũ bảo hiểm', 'áo khoác',
        'giày thể thao', 'balo học sinh', 'cặp sách', 'bút viết',
        'vở ghi chép', 'sổ tay', 'bút chì', 'tẩy',
        'thước kẻ', 'compa', 'máy tính cầm tay', 'điện thoại cũ'
    ];

    protected $vietnameseDescriptions = [
        'Tình trạng tốt, sử dụng cẩn thận',
        'Còn mới, ít sử dụng',
        'Đã qua sử dụng nhưng còn tốt',
        'Cần sửa chữa nhỏ',
        'Hoạt động bình thường',
        'Phù hợp cho sinh viên',
        'Giá cả hợp lý',
        'Giao hàng tận nơi',
        'Có thể thương lượng giá',
        'Liên hệ để biết thêm chi tiết'
    ];

    protected $conditions = ['A', 'B', 'C', 'D'];
    protected $statuses = ['active', 'pending', 'sold'];

    /**
     * 🏭 Generate synthetic listings data
     */
    public function generateListings(int $count = 1000): array
    {
        $listings = [];
        $users = User::pluck('id')->toArray();
        $categories = Category::pluck('id')->toArray();

        if (empty($users)) {
            throw new \Exception('No users found. Please seed users first.');
        }

        if (empty($categories)) {
            throw new \Exception('No categories found. Please seed categories first.');
        }

        for ($i = 0; $i < $count; $i++) {
            $listings[] = [
                'id' => 'test_' . ($i + 1),
                'title' => $this->generateVietnameseTitle(),
                'description' => $this->generateVietnameseDescription(),
                'price' => $this->generatePrice(),
                'condition_grade' => $this->conditions[array_rand($this->conditions)],
                'status' => $this->statuses[array_rand($this->statuses)],
                'category_id' => $categories[array_rand($categories)],
                'seller_id' => $users[array_rand($users)],
                'created_at' => $this->generateRandomDate(),
                'updated_at' => $this->generateRandomDate(),
            ];
        }

        return $listings;
    }

    /**
     * 📚 Generate Vietnamese product titles
     */
    private function generateVietnameseTitle(): string
    {
        $baseProduct = $this->vietnameseProducts[array_rand($this->vietnameseProducts)];
        
        $modifiers = [
            'cũ', 'đã qua sử dụng', 'tình trạng tốt', 'còn mới',
            'giá rẻ', 'cần bán gấp', 'chất lượng cao', 'phù hợp',
            'cho sinh viên', 'tiết kiệm', 'thương lượng', 'giao hàng'
        ];

        $modifier = $modifiers[array_rand($modifiers)];
        
        return ucfirst($baseProduct) . ' ' . $modifier;
    }

    /**
     * 📝 Generate Vietnamese descriptions
     */
    private function generateVietnameseDescription(): string
    {
        $descriptions = [];
        $numSentences = rand(2, 4);
        
        for ($i = 0; $i < $numSentences; $i++) {
            $descriptions[] = $this->vietnameseDescriptions[array_rand($this->vietnameseDescriptions)];
        }
        
        return implode('. ', $descriptions) . '.';
    }

    /**
     * 💰 Generate realistic prices
     */
    private function generatePrice(): float
    {
        // Generate prices between 10,000 and 5,000,000 VND
        $minPrice = 10000;
        $maxPrice = 5000000;
        
        return round(rand($minPrice, $maxPrice) / 1000) * 1000; // Round to nearest 1000
    }

    /**
     * 📅 Generate random dates within last 6 months
     */
    private function generateRandomDate(): string
    {
        $start = now()->subMonths(6);
        $end = now();
        
        $randomTimestamp = rand($start->timestamp, $end->timestamp);
        
        return date('Y-m-d H:i:s', $randomTimestamp);
    }

    /**
     * 🧪 Generate test queries for Vietnamese language testing
     */
    public function generateVietnameseTestQueries(): array
    {
        return [
            'sách giáo khoa' => 'Vietnamese with diacritics',
            'sach giao khoa' => 'Vietnamese without diacritics',
            'sách toán lớp 10' => 'Complex Vietnamese phrase',
            'sach toan lop 10' => 'Complex Vietnamese without diacritics',
            'máy tính bảng' => 'Vietnamese with special characters',
            'may tinh bang' => 'Vietnamese without special characters',
            'laptop cũ giá rẻ' => 'Multi-word Vietnamese query',
            'laptop cu gia re' => 'Multi-word without diacritics',
            'điện thoại' => 'Vietnamese with special characters',
            'dien thoai' => 'Vietnamese without special characters',
            'bàn học sinh viên' => 'Long Vietnamese phrase',
            'ban hoc sinh vien' => 'Long phrase without diacritics'
        ];
    }

    /**
     * 🔍 Generate test queries for performance testing
     */
    public function generatePerformanceTestQueries(): array
    {
        return [
            'sách' => 'Single word',
            'laptop' => 'Single word',
            'máy tính' => 'Two words with diacritics',
            'sách giáo khoa' => 'Three words',
            'laptop cũ giá rẻ' => 'Four words',
            'sách toán lớp 10 cũ' => 'Five words',
            'máy tính bảng cũ tình trạng tốt' => 'Six words',
            'laptop dell cũ giá rẻ cho sinh viên' => 'Seven words',
            'sách giáo khoa toán lớp 10 cũ tình trạng tốt' => 'Eight words'
        ];
    }

    /**
     * 📊 Generate test data for different dataset sizes
     */
    public function generateTestDatasets(): array
    {
        $datasets = [];
        
        // Small dataset (100 records)
        $datasets['small'] = [
            'count' => 100,
            'description' => 'Small dataset for basic testing',
            'listings' => $this->generateListings(100)
        ];
        
        // Medium dataset (1,000 records)
        $datasets['medium'] = [
            'count' => 1000,
            'description' => 'Medium dataset for moderate testing',
            'listings' => $this->generateListings(1000)
        ];
        
        // Large dataset (10,000 records)
        $datasets['large'] = [
            'count' => 10000,
            'description' => 'Large dataset for stress testing',
            'listings' => $this->generateListings(10000)
        ];
        
        return $datasets;
    }

    /**
     * 🎯 Generate specific test scenarios
     */
    public function generateTestScenarios(): array
    {
        return [
            'simple_search' => [
                'name' => 'Simple Search',
                'description' => 'Single keyword searches',
                'queries' => ['sách', 'laptop', 'máy tính', 'bàn học']
            ],
            'complex_search' => [
                'name' => 'Complex Search',
                'description' => 'Multi-keyword searches with filters',
                'queries' => [
                    'sách giáo khoa toán',
                    'laptop cũ giá rẻ',
                    'máy tính bảng tình trạng tốt'
                ]
            ],
            'vietnamese_search' => [
                'name' => 'Vietnamese Language Search',
                'description' => 'Vietnamese text with and without diacritics',
                'queries' => $this->generateVietnameseTestQueries()
            ],
            'performance_search' => [
                'name' => 'Performance Search',
                'description' => 'Various query lengths for performance testing',
                'queries' => $this->generatePerformanceTestQueries()
            ],
            'fuzzy_search' => [
                'name' => 'Fuzzy Search',
                'description' => 'Queries with typos and variations',
                'queries' => [
                    'sach' => 'sách',
                    'may tinh' => 'máy tính',
                    'lapto' => 'laptop',
                    'ban hoc' => 'bàn học'
                ]
            ]
        ];
    }

    /**
     * 📈 Generate load test scenarios
     */
    public function generateLoadTestScenarios(): array
    {
        return [
            'light_load' => [
                'concurrent_users' => 10,
                'queries_per_user' => 5,
                'duration_minutes' => 5,
                'description' => 'Light load testing'
            ],
            'medium_load' => [
                'concurrent_users' => 50,
                'queries_per_user' => 10,
                'duration_minutes' => 10,
                'description' => 'Medium load testing'
            ],
            'heavy_load' => [
                'concurrent_users' => 100,
                'queries_per_user' => 20,
                'duration_minutes' => 15,
                'description' => 'Heavy load testing'
            ],
            'stress_load' => [
                'concurrent_users' => 200,
                'queries_per_user' => 30,
                'duration_minutes' => 20,
                'description' => 'Stress testing'
            ]
        ];
    }

    /**
     * 🗂️ Generate categories for testing
     */
    public function generateTestCategories(): array
    {
        return [
            ['name' => 'Sách giáo khoa', 'slug' => 'sach-giao-khoa'],
            ['name' => 'Sách tham khảo', 'slug' => 'sach-tham-khao'],
            ['name' => 'Máy tính', 'slug' => 'may-tinh'],
            ['name' => 'Điện thoại', 'slug' => 'dien-thoai'],
            ['name' => 'Bàn ghế', 'slug' => 'ban-ghe'],
            ['name' => 'Xe cộ', 'slug' => 'xe-co'],
            ['name' => 'Quần áo', 'slug' => 'quan-ao'],
            ['name' => 'Đồ dùng học tập', 'slug' => 'do-dung-hoc-tap']
        ];
    }

    /**
     * 👥 Generate test users
     */
    public function generateTestUsers(int $count = 50): array
    {
        $users = [];
        
        for ($i = 0; $i < $count; $i++) {
            $users[] = [
                'name' => 'Test User ' . ($i + 1),
                'email' => 'testuser' . ($i + 1) . '@tdc.edu.vn',
                'password' => bcrypt('password123'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        return $users;
    }

    /**
     * 📊 Get statistics about generated data
     */
    public function getDataStatistics(array $listings): array
    {
        $prices = array_column($listings, 'price');
        $conditions = array_column($listings, 'condition_grade');
        $statuses = array_column($listings, 'status');
        
        return [
            'total_listings' => count($listings),
            'price_range' => [
                'min' => min($prices),
                'max' => max($prices),
                'avg' => round(array_sum($prices) / count($prices), 2)
            ],
            'condition_distribution' => array_count_values($conditions),
            'status_distribution' => array_count_values($statuses),
            'date_range' => [
                'earliest' => min(array_column($listings, 'created_at')),
                'latest' => max(array_column($listings, 'created_at'))
            ]
        ];
    }
}
