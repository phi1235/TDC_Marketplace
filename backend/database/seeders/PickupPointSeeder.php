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
                'name'         => 'Cổng chính TDC',
                'campus_code'  => 'TDC-MAIN',
                'address'      => '53 Võ Văn Ngân, Phường Linh Chiểu, Thủ Đức',
                'lat'          => 10.8505,
                'lng'          => 106.7717,
                'opening_hours'=> json_encode(['mon-fri' => '06:00-22:00', 'sat-sun' => '07:00-20:00']),
                'is_active'    => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Thư viện TDC (tầng 1)',
                'campus_code'  => 'TDC-LIB',
                'address'      => 'Nhà E, Khu học tập TDC',
                'lat'          => 10.8508,
                'lng'          => 106.7720,
                'opening_hours'=> json_encode(['mon-fri' => '07:00-21:00', 'sat' => '08:00-17:00']),
                'is_active'    => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Sảnh nhà A (Hành chính)',
                'campus_code'  => 'TDC-A',
                'address'      => 'Nhà A - Khu hành chính',                
                'lat'          => 10.8502,
                'lng'          => 106.7715,
                'opening_hours'=> json_encode(['mon-fri' => '07:30-17:00']),
                'is_active'    => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Căng tin sinh viên',
                'campus_code'  => 'TDC-CANTEEN',
                'address'      => 'Tầng 1, Khu B - TDC',
                'lat'          => 10.8510,
                'lng'          => 106.7722,
                'opening_hours'=> json_encode(['mon-sun' => '06:30-20:00']),
                'is_active'    => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Khu vực sân bóng',
                'campus_code'  => 'TDC-SPORT',
                'address'      => 'Sân thể thao TDC',
                'lat'          => 10.8515,
                'lng'          => 106.7725,
                'opening_hours'=> json_encode(['mon-sun' => '06:00-21:00']),
                'is_active'    => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Phòng Đoàn - Hội (Nhà C)',
                'campus_code'  => 'TDC-UNION',
                'address'      => 'Tầng 2, Nhà C',
                'lat'          => 10.8507,
                'lng'          => 106.7718,
                'opening_hours'=> json_encode(['mon-fri' => '08:00-17:00']),
                'is_active'    => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Khu đậu xe sinh viên',
                'campus_code'  => 'TDC-PARKING',
                'address'      => 'Bãi xe khu A',
                'lat'          => 10.8500,
                'lng'          => 106.7713,
                'opening_hours'=> json_encode(['mon-sun' => '06:00-22:00']),
                'is_active'    => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Quầy photocopy (cạnh thư viện)',
                'campus_code'  => 'TDC-PHOTO',
                'address'      => 'Cạnh thư viện, khu E',
                'lat'          => 10.8509,
                'lng'          => 106.7721,
                'opening_hours'=> json_encode(['mon-fri' => '07:00-19:00', 'sat' => '08:00-15:00']),
                'is_active'    => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }
}
