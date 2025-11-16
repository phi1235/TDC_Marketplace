<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Major;
use App\Models\SellerProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Get all majors for random assignment
        $majorIds = Major::pluck('id')->toArray();

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@tdc.edu.vn'],
            [
                'name' => 'Admin TDC',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
                'major_id' => null, // Admin không cần ngành
            ]
        );
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Create sample users
        $users = [
            [
                'name' => 'Nguyễn Văn A',
                'email' => 'nguyenvana@tdc.edu.vn',
                'phone' => '0123456789',
                'academic_year' => '2023-2024',
                'major' => 'Công nghệ thông tin',
            ],
            [
                'name' => 'Trần Thị B',
                'email' => 'tranthib@tdc.edu.vn',
                'phone' => '0987654321',
                'academic_year' => '2022-2023',
                'major' => 'Kế toán',
            ],
            [
                'name' => 'Lê Văn C',
                'email' => 'levanc@tdc.edu.vn',
                'phone' => '0369258147',
                'academic_year' => '2024-2025',
                'major' => 'Điện tử',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'phone' => $userData['phone'],
                    'role' => 'user',
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'major_id' => $majorIds[array_rand($majorIds)] ?? null, // Random major
                ]
            );

            if (!$user->hasRole('user')) {
                $user->assignRole('user');
            }

            // Create seller profile
            SellerProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'student_id' => 'SV' . rand(100000, 999999),
                    'verified_student' => true,
                    'verified_at' => now(),
                    'rating' => rand(35, 50) / 10, // 3.5 - 5.0
                    'total_ratings' => rand(5, 20),
                    'total_sales' => rand(0, 10),
                    'total_revenue' => rand(0, 5000000),
                    'bio' => 'Sinh viên ' . $userData['major'],
                    'academic_year' => $userData['academic_year'],
                    'major' => $userData['major'],
                ]
            );
        }
    }
}
