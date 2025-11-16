<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LegalDoc;
use Illuminate\Support\Str;

class LegalDocSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Terms of Service (Điều khoản sử dụng)
        LegalDoc::create([
            'type' => 'terms',
            'title' => 'Điều khoản sử dụng TDC Marketplace',
            'slug' => 'terms-of-service',
            'content' => $this->getTermsContent(),
            'version' => 'v1.0.0',
            'is_active' => true,
        ]);

        // 2. Privacy Policy (Chính sách bảo mật)
        LegalDoc::create([
            'type' => 'privacy',
            'title' => 'Chính sách bảo mật',
            'slug' => 'privacy-policy',
            'content' => $this->getPrivacyContent(),
            'version' => 'v1.0.0',
            'is_active' => true,
        ]);

        // 3. Community Guidelines (Quy tắc cộng đồng)
        LegalDoc::create([
            'type' => 'guidelines',
            'title' => 'Quy tắc cộng đồng',
            'slug' => 'community-guidelines',
            'content' => $this->getGuidelinesContent(),
            'version' => 'v1.0.0',
            'is_active' => true,
        ]);
    }

    private function getTermsContent(): string
    {
        return <<<'EOT'
# Điều khoản sử dụng TDC Marketplace

## 1. Giới thiệu
Chào mừng bạn đến với TDC Marketplace — nền tảng giao dịch học liệu và đồ dùng học tập dành cho sinh viên Trường Cao đẳng Công nghệ Thủ Đức. Khi truy cập hoặc sử dụng dịch vụ, bạn đồng ý chịu sự ràng buộc của Điều khoản này.

## 2. Đối tượng & Tài khoản
- Dịch vụ dành cho sinh viên có email @tdc.edu.vn
- Bạn cam kết cung cấp thông tin chính xác, đầy đủ
- Bạn chịu trách nhiệm bảo mật thông tin đăng nhập
- Chúng tôi có thể tạm khóa tài khoản nếu phát hiện vi phạm

## 3. Quy tắc nội dung & đăng tin
- Không đăng nội dung vi phạm pháp luật, đồi trụy, bạo lực
- Không spam, quảng cáo trái phép
- Không bán hàng cấm, hàng giả, hàng lậu
- Tin đăng phải chính xác về giá cả, tình trạng hàng

## 4. Mua bán & Giao dịch
- Người mua và người bán tự thỏa thuận
- Chúng tôi chỉ cung cấp nền tảng kết nối
- Hệ thống Escrow giúp đảm bảo an toàn giao dịch
- Giao dịch tại các điểm pickup được khuyến khích

## 5. Phí & Thuế
- Một số tính năng có thể thu phí
- Mức phí sẽ được công bố rõ ràng
- Bạn tự chịu trách nhiệm kê khai thuế (nếu có)

## 6. Quyền riêng tư & Bảo mật
- Chúng tôi cam kết bảo vệ thông tin cá nhân
- Xem chi tiết tại Chính sách bảo mật
- Không chia sẻ thông tin cho bên thứ ba khi chưa được phép

## 7. Sở hữu trí tuệ
- Nội dung, thương hiệu thuộc quyền TDC Marketplace
- Không được sao chép, phân phối trái phép

## 8. Trách nhiệm & Miễn trừ
- Chúng tôi không chịu trách nhiệm về tranh chấp giữa người dùng
- Chúng tôi cố gắng hỗ trợ giải quyết tranh chấp
- Không bảo đảm tính chính xác tuyệt đối của thông tin

## 9. Sửa đổi Điều khoản
- Chúng tôi có thể cập nhật Điều khoản bất cứ lúc nào
- Thông báo sẽ được gửi qua email hoặc hiển thị trên website
- Việc tiếp tục sử dụng đồng nghĩa với việc chấp nhận thay đổi

## 10. Liên hệ
- Email: support@tdc.edu.vn
- Hotline: 0123 456 789
- Địa chỉ: Khoa CNTT - Trường Cao Đẳng Công Nghệ Thủ Đức
EOT;
    }

    private function getPrivacyContent(): string
    {
        return <<<'EOT'
# Chính sách bảo mật TDC Marketplace

## 1. Giới thiệu
TDC Marketplace cam kết bảo vệ quyền riêng tư và thông tin cá nhân của người dùng.

## 2. Thu thập thông tin
Chúng tôi thu thập:
- Họ tên, email, số điện thoại
- Thông tin tài khoản đăng nhập
- Dữ liệu kỹ thuật (IP, browser, thiết bị)
- Nội dung đăng tải (tin rao, bình luận, hình ảnh)

## 3. Sử dụng thông tin
Thông tin được sử dụng để:
- Xác thực và quản lý tài khoản
- Cung cấp dịch vụ marketplace
- Cải thiện trải nghiệm người dùng
- Liên lạc về giao dịch và thông báo quan trọng
- Phân tích và thống kê

## 4. Chia sẻ thông tin
- Không bán thông tin cho bên thứ ba
- Có thể chia sẻ khi có yêu cầu pháp lý
- Chia sẻ với đối tác cung cấp dịch vụ (thanh toán, vận chuyển)

## 5. Bảo mật
- Mã hóa dữ liệu nhạy cảm
- Hệ thống firewall và giám sát 24/7
- Backup định kỳ
- Tuân thủ các chuẩn bảo mật quốc tế

## 6. Quyền của người dùng
Bạn có quyền:
- Xem, cập nhật thông tin cá nhân
- Yêu cầu xóa tài khoản
- Từ chối nhận email marketing
- Khiếu nại về việc xử lý dữ liệu

## 7. Cookie
- Sử dụng cookie để cải thiện trải nghiệm
- Bạn có thể tắt cookie trong trình duyệt
- Một số tính năng có thể bị ảnh hưởng khi tắt cookie

## 8. Thay đổi chính sách
Chúng tôi có thể cập nhật chính sách này. Thay đổi sẽ được thông báo trên website.

## 9. Liên hệ
Nếu có thắc mắc về chính sách bảo mật:
- Email: privacy@tdc.edu.vn
- Hotline: 0123 456 789
EOT;
    }

    private function getGuidelinesContent(): string
    {
        return <<<'EOT'
# Quy tắc cộng đồng TDC Marketplace

## 1. Tôn trọng
- Tôn trọng người dùng khác
- Không xúc phạm, quấy rối, phân biệt đối xử
- Không spam, troll

## 2. Trung thực
- Cung cấp thông tin chính xác
- Không gian lận, lừa đảo
- Không đăng tin giả, tin sai sự thật

## 3. An toàn
- Giao dịch tại địa điểm công cộng
- Sử dụng hệ thống Escrow để bảo vệ quyền lợi
- Báo cáo ngay khi phát hiện vi phạm

## 4. Nội dung
- Không đăng nội dung vi phạm pháp luật
- Không đăng nội dung bạo lực, đồi trụy
- Không vi phạm bản quyền

## 5. Giao dịch
- Mô tả sản phẩm rõ ràng, trung thực
- Giá cả hợp lý
- Giao hàng đúng cam kết

## 6. Hình phạt
Vi phạm quy tắc có thể dẫn đến:
- Cảnh báo
- Tạm khóa tài khoản
- Khóa vĩnh viễn
- Báo cáo cơ quan chức năng (nếu nghiêm trọng)
EOT;
    }
}
