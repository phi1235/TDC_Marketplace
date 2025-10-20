<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Wishlist; // nhớ có model Wishlist
use App\Models\User;
use App\Models\Listing;
use Carbon\Carbon;

class WishlistSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::first();        // user đầu tiên
        $user2 = User::skip(1)->first(); // user thứ 2

        $listing1 = Listing::first();         // listing 1
        $listing2 = Listing::skip(1)->first(); // listing 2
        $listing3 = Listing::skip(2)->first(); // listing 3

        $now = Carbon::now();

        $data = [
            [
                'user_id' => $user1->id,
                'listing_id' => $listing1->id,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $user1->id,
                'listing_id' => $listing2->id,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $user2->id,
                'listing_id' => $listing3->id,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        Wishlist::insert($data);

        $this->command->info('WishlistSeeder done ✔');
    }
}
