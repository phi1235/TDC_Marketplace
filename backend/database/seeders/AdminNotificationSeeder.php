<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdminNotification;

class AdminNotificationSeeder extends Seeder
{
    public function run(): void
    {
        AdminNotification::create([
            'title' => 'Chào mừng đến với hệ thống!',
            'message' => 'Đây là thông báo mẫu đầu tiên từ admin.',
            'type' => 'welcome',
            'user_id' => null, // gửi cho tất cả user
        ]);
    }
}
