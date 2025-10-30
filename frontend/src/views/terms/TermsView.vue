<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 px-4 flex flex-col justify-between">
    <div class="max-w-4xl mx-auto w-full">
      <!-- 🧭 Tiêu đề -->
      <h1 class="text-3xl font-bold text-center mb-10 text-gray-900 dark:text-gray-100">
        📜 Điều khoản sử dụng
      </h1>

      <!-- Nội dung điều khoản -->
      <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 space-y-8 text-gray-700 dark:text-gray-300 leading-relaxed"
      >
        <!-- 1️⃣ Giới thiệu -->
        <section>
          <h2 class="text-2xl font-semibold mb-3 text-gray-900 dark:text-gray-100">
            1. Giới thiệu
          </h2>
          <p>
            Chào mừng bạn đến với <strong>TDC Marketplace</strong> — nền tảng trao đổi, mua bán đồ học tập dành cho sinh viên.
            Khi sử dụng trang web hoặc ứng dụng của chúng tôi, bạn đồng ý tuân thủ các điều khoản và điều kiện được nêu dưới đây.
          </p>
        </section>

        <!-- 2️⃣ Quyền và nghĩa vụ người dùng -->
        <section>
          <h2 class="text-2xl font-semibold mb-3 text-gray-900 dark:text-gray-100">
            2. Quyền và nghĩa vụ của người dùng
          </h2>
          <ul class="list-disc pl-6 space-y-2">
            <li>Người dùng phải cung cấp thông tin chính xác và chịu trách nhiệm về nội dung đăng tải.</li>
            <li>Không được đăng tải nội dung vi phạm pháp luật, đạo đức, hay bản quyền của bên thứ ba.</li>
            <li>TDC Marketplace có quyền tạm khóa tài khoản nếu phát hiện hành vi gian lận, spam hoặc vi phạm điều khoản.</li>
          </ul>
        </section>

        <!-- 3️⃣ Quyền và nghĩa vụ của TDC Marketplace -->
        <section>
          <h2 class="text-2xl font-semibold mb-3 text-gray-900 dark:text-gray-100">
            3. Quyền và nghĩa vụ của TDC Marketplace
          </h2>
          <ul class="list-disc pl-6 space-y-2">
            <li>Chúng tôi cam kết bảo vệ quyền riêng tư của người dùng và không chia sẻ dữ liệu cá nhân cho bên thứ ba nếu không có sự đồng ý.</li>
            <li>Chúng tôi có quyền chỉnh sửa, tạm ngưng hoặc chấm dứt dịch vụ khi cần thiết để bảo trì hoặc nâng cấp hệ thống.</li>
            <li>Không chịu trách nhiệm với nội dung, hình ảnh, hoặc giao dịch giữa các người dùng với nhau.</li>
          </ul>
        </section>

        <!-- 4️⃣ Chính sách nội dung -->
        <section>
          <h2 class="text-2xl font-semibold mb-3 text-gray-900 dark:text-gray-100">
            4. Chính sách nội dung
          </h2>
          <p>
            Các nội dung đăng tải trên nền tảng phải tuân thủ các quy định về bản quyền, không được chứa hình ảnh phản cảm, ngôn ngữ xúc phạm,
            hoặc thông tin sai lệch. Người dùng vi phạm sẽ bị cảnh báo hoặc xóa tài khoản tùy theo mức độ.
          </p>
        </section>

        <!-- 5️⃣ Sửa đổi điều khoản -->
        <section>
          <h2 class="text-2xl font-semibold mb-3 text-gray-900 dark:text-gray-100">
            5. Sửa đổi điều khoản
          </h2>
          <p>
            TDC Marketplace có thể cập nhật hoặc chỉnh sửa điều khoản này bất kỳ lúc nào.
            Khi có thay đổi, chúng tôi sẽ thông báo qua email hoặc thông báo trên trang chủ.
            Việc bạn tiếp tục sử dụng dịch vụ sau khi có thay đổi đồng nghĩa với việc bạn chấp nhận các điều khoản mới.
          </p>
        </section>

        <!-- 6️⃣ Liên hệ -->
        <section>
          <h2 class="text-2xl font-semibold mb-3 text-gray-900 dark:text-gray-100">
            6. Liên hệ
          </h2>
          <p>
            Mọi thắc mắc hoặc yêu cầu hỗ trợ, vui lòng liên hệ:
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
