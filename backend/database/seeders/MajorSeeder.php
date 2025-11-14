<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $majors = [
            [
                'name' => 'Công nghệ thông tin',
                'description' => 'Lập trình, phát triển phần mềm, quản trị hệ thống, an ninh mạng',
                'icon' => '💻',
                'display_order' => 1,
            ],
            [
                'name' => 'Điện - Điện tử',
                'description' => 'Kỹ thuật điện, điện tử công nghiệp, tự động hóa, điều khiển',
                'icon' => '⚡',
                'display_order' => 2,
            ],
            [
                'name' => 'Cơ khí',
                'description' => 'Thiết kế cơ khí, chế tạo máy, công nghệ ô tô, cơ điện tử',
                'icon' => '⚙️',
                'display_order' => 3,
            ],
            [
                'name' => 'Kế toán',
                'description' => 'Kế toán doanh nghiệp, kiểm toán, thuế, tài chính',
                'icon' => '📊',
                'display_order' => 4,
            ],
            [
                'name' => 'Quản trị kinh doanh',
                'description' => 'Quản trị doanh nghiệp, marketing, nhân sự, logistics',
                'icon' => '💼',
                'display_order' => 5,
            ],
            [
                'name' => 'Du lịch - Khách sạn',
                'description' => 'Quản trị khách sạn, hướng dẫn du lịch, lữ hành, nghiệp vụ buồng phòng',
                'icon' => '🏨',
                'display_order' => 6,
            ],
            [
                'name' => 'Ngoại ngữ',
                'description' => 'Tiếng Anh, tiếng Nhật, tiếng Hàn, tiếng Trung, biên - phiên dịch',
                'icon' => '🌐',
                'display_order' => 7,
            ],
            [
                'name' => 'Thiết kế đồ họa',
                'description' => 'Đồ họa quảng cáo, thiết kế đa phương tiện, UI/UX, animation',
                'icon' => '🎨',
                'display_order' => 8,
            ],
            [
                'name' => 'Marketing',
                'description' => 'Marketing số, truyền thông, quảng cáo, thương mại điện tử',
                'icon' => '📱',
                'display_order' => 9,
            ],
            [
                'name' => 'Xây dựng',
                'description' => 'Kỹ thuật xây dựng dân dụng, công nghiệp, kiến trúc, giám sát thi công',
                'icon' => '🏗️',
                'display_order' => 10,
            ],
            [
                'name' => 'Khác',
                'description' => 'Các ngành học khác không thuộc danh mục trên',
                'icon' => '📚',
                'display_order' => 11,
            ],
        ];

        foreach ($majors as $major) {
            Major::create([
                'name' => $major['name'],
                'slug' => Str::slug($major['name']),
                'description' => $major['description'],
                'icon' => $major['icon'],
                'is_active' => true,
                'display_order' => $major['display_order'],
            ]);
        }

        $this->command->info('✅ Đã tạo ' . count($majors) . ' ngành học TDC');
    }
}
