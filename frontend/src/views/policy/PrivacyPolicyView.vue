<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 px-4 flex flex-col justify-between">
    <div class="max-w-4xl mx-auto w-full">
      <!-- 🧭 Tiêu đề -->
      <h1 class="text-3xl font-bold text-center mb-10 text-gray-900 dark:text-gray-100">
        🔒 Chính sách bảo mật
      </h1>

      <!-- Nội dung chính -->
      <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 space-y-8 text-gray-700 dark:text-gray-300 leading-relaxed"
      >
        <!-- 1️⃣ Mục đích thu thập thông tin -->
        <section>
          <h2 class="text-2xl font-semibold mb-3 text-gray-900 dark:text-gray-100">
            1. Mục đích thu thập thông tin
          </h2>
          <p>
            Chúng tôi thu thập thông tin cá nhân của người dùng để cung cấp dịch vụ tốt hơn, bao gồm:
          </p>
          <ul class="list-disc pl-6 space-y-2">
            <li>Xác thực danh tính người dùng và bảo mật tài khoản.</li>
            <li>Hỗ trợ trong quá trình giao dịch, liên hệ hoặc giải quyết tranh chấp.</li>
            <li>Nâng cao trải nghiệm người dùng và tối ưu hóa nền tảng.</li>
          </ul>
        </section>

        <!-- 2️⃣ Loại thông tin được thu thập -->
        <section>
          <h2 class="text-2xl font-semibold mb-3 text-gray-900 dark:text-gray-100">
            2. Loại thông tin được thu thập
          </h2>
          <ul class="list-disc pl-6 space-y-2">
            <li>Họ tên, địa chỉ email, số điện thoại, và thông tin tài khoản.</li>
            <li>Dữ liệu kỹ thuật như địa chỉ IP, loại trình duyệt, thiết bị, và nhật ký truy cập.</li>
            <li>Nội dung người dùng đăng tải (bài đăng, tin rao, bình luận, hình ảnh).</li>
          </ul>
        </section>

        <!-- 3️⃣ Phạm vi sử dụng thông tin -->
        <section>
          <h2 class="text-2xl font-semibold mb-3 text-gray-900 dark:text-gray-100">
            3. Phạm vi sử dụng thông tin
          </h2>
          <p>
            TDC Marketplace chỉ sử dụng thông tin cá nhân cho mục đích vận hành hệ thống, liên hệ hỗ trợ,
            và đảm bảo quyền lợi của người dùng. Chúng tôi <strong>không chia sẻ dữ liệu</strong> với bên thứ ba trừ khi được người dùng đồng ý hoặc theo yêu cầu pháp luật.
          </p>
        </section>

        <!-- 4️⃣ Bảo mật và lưu trữ -->
        <section>
          <h2 class="text-2xl font-semibold mb-3 text-gray-900 dark:text-gray-100">
            4. Bảo mật và lưu trữ dữ liệu
          </h2>
          <p>
            Dữ liệu cá nhân được lưu trữ an toàn trên máy chủ có hệ thống bảo vệ nghiêm ngặt.
            Mọi truy cập trái phép, tiết lộ hoặc chỉnh sửa thông tin người dùng đều bị nghiêm cấm.
          </p>
          <p>
            Chúng tôi áp dụng các biện pháp mã hóa và tường lửa để giảm thiểu rủi ro mất mát dữ liệu.
          </p>
        </section>

        <!-- 5️⃣ Quyền của người dùng -->
        <section>
          <h2 class="text-2xl font-semibold mb-3 text-gray-900 dark:text-gray-100">
            5. Quyền của người dùng
          </h2>
          <ul class="list-disc pl-6 space-y-2">
            <li>Yêu cầu xem, chỉnh sửa hoặc xóa thông tin cá nhân của mình.</li>
            <li>Từ chối nhận các thông báo quảng cáo hoặc tiếp thị.</li>
            <li>Liên hệ với bộ phận hỗ trợ để khiếu nại hoặc yêu cầu bảo mật bổ sung.</li>
          </ul>
        </section>

        <!-- 6️⃣ Thay đổi chính sách -->
        <section>
          <h2 class="text-2xl font-semibold mb-3 text-gray-900 dark:text-gray-100">
            6. Thay đổi chính sách
          </h2>
          <p>
            Chính sách bảo mật này có thể được cập nhật định kỳ để phù hợp với thay đổi trong luật pháp hoặc mô hình hoạt động.
            Mọi thay đổi sẽ được công bố công khai trên trang chủ.
          </p>
        </section>

        <!-- 7️⃣ Liên hệ -->
        <section>
          <h2 class="text-2xl font-semibold mb-3 text-gray-900 dark:text-gray-100">
            7. Liên hệ
          </h2>
          <p>
            Nếu bạn có bất kỳ câu hỏi nào liên quan đến chính sách bảo mật, vui lòng liên hệ:
          </p>
          <ul class="list-disc pl-6 space-y-1">
            <li>Email: <a href="mailto:support@tdc-marketplace.vn" class="text-blue-600 dark:text-blue-400 hover:underline">support@tdc-marketplace.vn</a></li>
            <li>Hotline: <span class="font-semibold">0123 456 789</span></li>
            <li>Địa chỉ: Khoa CNTT - Trường Cao Đẳng Công Nghệ Thủ Đức</li>
          </ul>
        </section>
      </div>
    </div>

    <!-- 🦶 Footer -->
    <footer class="mt-16 text-center text-sm text-gray-500 dark:text-gray-400 py-6">
      © {{ new Date().getFullYear() }} TDC Marketplace — Mọi quyền được bảo lưu.
    </footer>
  </div>
</template>

<script setup>
</script>

<style scoped>
p {
  margin-bottom: 0.5rem;
}
</style>
