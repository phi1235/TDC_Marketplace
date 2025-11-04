<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PickupPointSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pickup_points')->insert([
            [
                'name'       => 'Cổng chính TDC',
                'address'    => '12 Nguyễn Văn Bảo, Khu A',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Thư viện TDC (tầng 1)',
                'address'    => 'Nhà E, khu B',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Sảnh nhà A',
                'address'    => 'Khu hành chính',                
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
