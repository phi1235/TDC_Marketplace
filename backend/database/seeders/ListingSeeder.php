<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Listing;
use App\Models\User;

class ListingSeeder extends Seeder
{
    public function run(): void
    {
        $seller = User::where('role', 'user')->first() ?? User::first();
        if (!$seller) { return; }

        // Approved sample
        Listing::create([
            'seller_id' => $seller->id,
            'category_id' => 1,
            'title' => 'Laptop học tập 14 inch',
            'description' => 'Máy chạy tốt, pin 5h.',
            'price' => 7500000,
            'condition' => 'good',
            'status' => 'approved',
            'location' => 'TDC Campus',
            'views_count' => 10,
            'approved_at' => now(),
            'approved_by' => User::where('role', 'admin')->value('id'),
        ]);

        // Pending sample
        $pendingSamples = [
            ['Giáo trình toán cao cấp', 'Sách còn mới 90%.', 120000],
            ['Tai nghe không dây', 'Âm thanh tốt, pin bền.', 350000],
            ['Balo sinh viên', 'Balo chống nước, còn mới.', 200000],
        ];

        foreach ($pendingSamples as [$title, $desc, $price]) {
            Listing::create([
                'seller_id' => $seller->id,
                'category_id' => 1,
                'title' => $title,
                'description' => $desc,
                'price' => $price,
                'condition' => 'good',
                'status' => 'pending',
                'location' => 'Khu A',
                'views_count' => 0,
            ]);
        }

        // Rejected sample
        Listing::create([
            'seller_id' => $seller->id,
            'category_id' => 1,
            'title' => 'Điện thoại cũ',
            'description' => 'Máy xước nhẹ.',
            'price' => 900000,
            'condition' => 'fair',
            'status' => 'rejected',
            'location' => 'Khu B',
            'views_count' => 0,
            'rejected_at' => now(),
            'rejected_by' => User::where('role', 'admin')->value('id'),
            'rejection_reason' => 'policy_violation',
        ]);
    }
}


