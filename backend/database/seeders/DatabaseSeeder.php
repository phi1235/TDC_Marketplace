<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\FollowSeller;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CategorySeeder::class,
            CampusPickupSeeder::class,
            UserSeeder::class,
            ListingSeeder::class,
            //
            FollowSellersSeeder::class,
            WishlistSeeder::class,
            //diem giao dich
            PickupPointSeeder::class,    // bảng mới API đang dùng
        ]);
    }
}
