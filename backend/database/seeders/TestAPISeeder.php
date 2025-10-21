<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TestAPI;

class TestAPISeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TestAPI::create([
            'id' => '1',
            'name' => 'Truong Tuan Dung',
            'email' => 'truongtuandung2001@gmail.com',
            'description' => '....',
            'age' => 24,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        TestAPI::create([
            'id' => '2',
            'name' => 'Nguyen Van A',
            'email' => 'nguyenvana@gmail.com',
            'description' => 'toi la nguyen van a',
            'age' => 30,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        TestAPI::create([
            'id' => '3',
            'name' => 'Nguyen Thi B',
            'email' => 'nguyenthib@gmail.com',
            'description' => 'toi la nguyen thi b',
            'age' => 24,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
