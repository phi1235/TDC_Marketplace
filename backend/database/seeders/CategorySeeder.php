<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Sách giáo khoa',
                'slug' => 'sach-giao-khoa',
                'description' => 'Sách giáo khoa các môn học',
                'icon' => 'book',
                'is_active' => true,
            ],
            [
                'name' => 'Sách tham khảo',
                'slug' => 'sach-tham-khao',
                'description' => 'Sách tham khảo, tài liệu học tập',
                'icon' => 'academic-cap',
                'is_active' => true,
            ],
            [
                'name' => 'Đồ dùng học tập',
                'slug' => 'do-dung-hoc-tap',
                'description' => 'Bút, vở, thước kẻ, máy tính...',
                'icon' => 'pencil',
                'is_active' => true,
            ],
            [
                'name' => 'Điện tử',
                'slug' => 'dien-tu',
                'description' => 'Máy tính, laptop, điện thoại...',
                'icon' => 'computer-desktop',
                'is_active' => true,
            ],
            [
                'name' => 'Quần áo',
                'slug' => 'quan-ao',
                'description' => 'Quần áo, giày dép, phụ kiện',
                'icon' => 'shopping-bag',
                'is_active' => true,
            ],
            [
                'name' => 'Đồ nội thất',
                'slug' => 'do-noi-that',
                'description' => 'Bàn, ghế, tủ, kệ...',
                'icon' => 'home',
                'is_active' => true,
            ],
            [
                'name' => 'Thể thao',
                'slug' => 'the-thao',
                'description' => 'Dụng cụ thể thao, quần áo thể thao',
                'icon' => 'sport',
                'is_active' => true,
            ],
            [
                'name' => 'Khác',
                'slug' => 'khac',
                'description' => 'Các mặt hàng khác',
                'icon' => 'ellipsis-horizontal',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
