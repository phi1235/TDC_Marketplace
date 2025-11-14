<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 px-4">
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">
          📧 Liên hệ với chúng tôi
        </h1>
        <p class="text-gray-600 dark:text-gray-400 text-lg">
          Có câu hỏi hoặc cần hỗ trợ? Gửi tin nhắn cho chúng tôi!
        </p>
      </div>

      <div class="grid md:grid-cols-2 gap-8">
        <!-- Email Contact Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
          <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-6">
            📧 Liên hệ qua Email
          </h2>
          
          <div class="space-y-6">
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-6">
              <div class="flex items-center space-x-4 mb-4">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                  <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Email hỗ trợ</h3>
                  <p class="text-sm text-gray-600 dark:text-gray-400">Gửi email trực tiếp cho chúng tôi</p>
                </div>
              </div>
              <a 
                href="mailto:support@tdc.edu.vn"
                class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center font-semibold py-3 rounded-lg transition-colors"
              >
                ✉️ support@tdc.edu.vn
              </a>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6">
              <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">
                💡 Mẹo gửi email hiệu quả:
              </h4>
              <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                <li class="flex items-start space-x-2">
                  <span class="text-blue-600 mt-0.5">•</span>
                  <span>Tiêu đề email rõ ràng, ngắn gọn</span>
                </li>
                <li class="flex items-start space-x-2">
                  <span class="text-blue-600 mt-0.5">•</span>
                  <span>Mô tả chi tiết vấn đề của bạn</span>
                </li>
                <li class="flex items-start space-x-2">
                  <span class="text-blue-600 mt-0.5">•</span>
                  <span>Đính kèm ảnh chụp màn hình nếu cần</span>
                </li>
                <li class="flex items-start space-x-2">
                  <span class="text-blue-600 mt-0.5">•</span>
                  <span>Ghi rõ thông tin liên hệ của bạn</span>
                </li>
              </ul>
            </div>

            <p class="text-sm text-gray-500 dark:text-gray-400 text-center">
              ⏱️ Thời gian phản hồi: <span class="font-medium text-gray-900 dark:text-gray-100">1-2 ngày làm việc</span>
            </p>
          </div>
        </div>

        <!-- Contact Info -->
        <div class="space-y-6">
          <!-- Info Card -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">
              Thông tin liên hệ
            </h3>

            <div class="space-y-4">
              <!-- Address -->
              <div class="flex items-start space-x-4">
                <div class="text-blue-600 text-2xl">📍</div>
                <div>
                  <h4 class="font-medium text-gray-900 dark:text-gray-100">Địa chỉ</h4>
                  <p class="text-gray-600 dark:text-gray-400 text-sm">
                    53 Võ Văn Ngân, Linh Chiểu, Thủ Đức, TP.HCM
                  </p>
                </div>
              </div>

              <!-- Phone -->
              <div class="flex items-start space-x-4">
                <div class="text-blue-600 text-2xl">📞</div>
                <div>
                  <h4 class="font-medium text-gray-900 dark:text-gray-100">Điện thoại</h4>
                  <p class="text-gray-600 dark:text-gray-400 text-sm">
                    (028) 3896 3211
                  </p>
                </div>
              </div>

              <!-- Email -->
              <div class="flex items-start space-x-4">
                <div class="text-blue-600 text-2xl">✉️</div>
                <div>
                  <h4 class="font-medium text-gray-900 dark:text-gray-100">Email</h4>
                  <p class="text-gray-600 dark:text-gray-400 text-sm">
                    support@tdc.edu.vn
                  </p>
                </div>
              </div>

              <!-- Working Hours -->
              <div class="flex items-start space-x-4">
                <div class="text-blue-600 text-2xl">🕐</div>
                <div>
                  <h4 class="font-medium text-gray-900 dark:text-gray-100">Giờ làm việc</h4>
                  <p class="text-gray-600 dark:text-gray-400 text-sm">
                    Thứ 2 - Thứ 6: 8:00 - 17:00<br>
                    Thứ 7: 8:00 - 12:00
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- FAQ Link -->
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 border border-blue-200 dark:border-gray-600">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
              💡 Bạn có câu hỏi?
            </h3>
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
              Xem các câu hỏi thường gặp để tìm câu trả lời nhanh chóng!
            </p>
            <router-link
              to="/faq"
              class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
            >
              Xem FAQ →
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// No form handling needed - direct email contact
</script>
