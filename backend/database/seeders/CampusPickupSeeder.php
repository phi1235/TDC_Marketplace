<?php

namespace Database\Seeders;

use App\Models\CampusPickup;
use Illuminate\Database\Seeder;

class CampusPickupSeeder extends Seeder
{
    public function run(): void
    {
        $pickups = [
            [
                'name' => 'Cổng chính trường',
                'description' => 'Điểm gặp tại cổng chính trường Cao đẳng Công nghệ Thủ Đức',
                'building' => 'Cổng chính',
                'is_active' => true,
            ],
            [
                'name' => 'Thư viện',
                'description' => 'Điểm gặp tại thư viện trường',
                'building' => 'Tòa A',
                'floor' => 'Tầng 2',
                'room' => 'Thư viện',
                'is_active' => true,
            ],
            [
                'name' => 'Căng tin',
                'description' => 'Điểm gặp tại căng tin trường',
                'building' => 'Tòa B',
                'floor' => 'Tầng 1',
                'room' => 'Căng tin',
                'is_active' => true,
            ],
            [
                'name' => 'Sân thể thao',
                'description' => 'Điểm gặp tại sân thể thao',
                'building' => 'Sân thể thao',
                'is_active' => true,
            ],
            [
                'name' => 'Ký túc xá',
                'description' => 'Điểm gặp tại ký túc xá sinh viên',
                'building' => 'Ký túc xá',
                'is_active' => true,
            ],
        ];

        foreach ($pickups as $pickup) {
            CampusPickup::create($pickup);
        }
    }
}
