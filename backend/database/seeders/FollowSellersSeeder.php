<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FollowSellersSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('follow_sellers')->insert([
            [
                'user_id' => 2,   // user theo dõi
                'seller_id' => 3, // seller được follow
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'seller_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'seller_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
