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
            MajorSeeder::class,          // Seed majors TRƯỚC users và listings
            CampusPickupSeeder::class,
            PickupPointSeeder::class,    // Phải seed TRƯỚC ListingSeeder
            LegalDocSeeder::class,       // 📜 Seed legal documents (terms, privacy, guidelines)
            UserSeeder::class,
            ListingSeeder::class,
            //
            FollowSellersSeeder::class,
            WishlistSeeder::class,
            //
            AdminNotificationSeeder::class,
        ]);
    }
}
