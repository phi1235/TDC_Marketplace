<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AISupportSeeder extends Seeder
{
    /**
     * Ensure a dedicated AI Support admin exists for support chat.
     */
    public function run(): void
    {
        $aiSupport = User::firstOrCreate(
            ['email' => 'ai-support@tdc.edu.vn'],
            [
                'name' => 'AI Support',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        if (!$aiSupport->hasRole('admin')) {
            $aiSupport->assignRole('admin');
        }
    }
}
