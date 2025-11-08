<!-- src/views/faq/FaqContactView.vue -->
<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 px-4 flex flex-col justify-between">
    <div class="max-w-4xl mx-auto w-full">
      <h1 class="text-3xl font-bold text-center mb-8 text-gray-900 dark:text-gray-100">❓ Câu hỏi thường gặp (FAQ)</h1>

      <!-- FAQ -->
      <div class="space-y-4 mb-16">
        <div v-for="(item, i) in faqs" :key="i" class="bg-white dark:bg-gray-800 rounded-xl shadow-md">
          <button @click="toggle(i)" class="w-full flex justify-between items-center p-5 text-left">
            <div class="flex items-center space-x-2">
              <span class="text-blue-600 text-lg">💬</span>
              <span class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ item.question }}</span>
            </div>
            <span class="text-gray-600 dark:text-gray-300 text-xl transition-transform"
              :class="{ 'rotate-180': activeIndex === i }">▼</span>
          </button>
          <div v-if="activeIndex === i" class="px-6 pb-5 text-gray-700 dark:text-gray-300 border-t dark:border-gray-700">
            {{ item.answer }}
          </div>
        </div>
      </div>

      <!-- Contact -->
      <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 text-center space-y-6 border-t dark:border-gray-700">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">📞 Cần hỗ trợ thêm?</h2>
        <p class="text-gray-600 dark:text-gray-400">Nếu bạn không tìm thấy câu trả lời phù hợp, hãy gửi yêu cầu hỗ trợ
          cho chúng tôi.</p>

        <form @submit.prevent="onSubmit" class="max-w-md mx-auto space-y-4 text-left">
          <div>
            <label class="block text-sm font-medium mb-1">Họ và tên</label>
            <input v-model="form.name" type="text" required
              class="w-full p-3 rounded-lg border bg-gray-50 dark:bg-gray-900" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input v-model="form.email" type="email" required
              class="w-full p-3 rounded-lg border bg-gray-50 dark:bg-gray-900" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Chủ đề (tuỳ chọn)</label>
            <!-- Đổi từ form.topic -> form.subject để khớp BE -->
            <select v-model="form.subject" class="w-full p-3 rounded-lg border bg-gray-50 dark:bg-gray-900">
              <option value="">— Chọn chủ đề —</option>
              <option value="account">Tài khoản & đăng nhập</option>
              <option value="listing">Tin rao & hình ảnh</option>
              <option value="payment">Thanh toán</option>
              <option value="other">Khác</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Nội dung cần hỗ trợ</label>
            <textarea v-model="form.message" required rows="4" minlength="10"
              class="w-full p-3 rounded-lg border bg-gray-50 dark:bg-gray-900"></textarea>
          </div>

          <button :disabled="loading" type="submit"
            class="w-full bg-blue-600 text-white font-semibold py-3 rounded-lg hover:bg-blue-700 disabled:opacity-60">
            {{ loading ? 'Đang gửi...' : '📧 Gửi yêu cầu' }}
          </button>
        </form>

        <p v-if="sent" class="text-green-600 dark:text-green-400 font-medium mt-2">✅ Yêu cầu của bạn đã được gửi!</p>
        <p v-if="errorMsg" class="text-red-600 font-medium mt-2">❌ {{ errorMsg }}</p>
      </div>
    </div>

    <footer class="mt-16 text-center text-sm text-gray-500 dark:text-gray-400 py-6">
      © {{ new Date().getFullYear() }} TDC Marketplace — Mọi quyền được bảo lưu.
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
// Dùng service gửi mail-only:
import { sendContactMail } from '@/services/support'


const activeIndex = ref < number | null > (null)
const loading = ref(false)
const sent = ref(false)
const errorMsg = ref < string > ('')

const faqs = ref([
  { question: 'Làm sao để đăng tin rao mới?', answer: 'Đăng nhập → Dashboard → “Đăng tin mới”.' },
  { question: 'Tôi có thể chỉnh sửa tin rao không?', answer: 'Vào “Tin của tôi” trong Dashboard để chỉnh sửa.' },
  { question: 'Bật chế độ tối (Dark Mode) như thế nào?', answer: 'Dùng nút 🌙/☀️ trên cùng bên phải.' },
  { question: 'Tin bị từ chối thì làm gì?', answer: 'Xem lý do trong Dashboard rồi chỉnh sửa lại.' },
  { question: 'Liên hệ quản trị viên ở đâu?', answer: 'Gửi form hỗ trợ phía dưới hoặc email support@tdc-marketplace.vn.' },
])

// Đồng bộ với BE: name, email, subject, message
const form = ref({ name: '', email: '', subject: '', message: '' })

function toggle(i: number) {
  activeIndex.value = activeIndex.value === i ? null : i
}

async function onSubmit() {
  errorMsg.value = ''
  sent.value = false
  loading.value = true
  try {
    await sendContactMail({
      name: form.value.name,
      email: form.value.email,
      subject: form.value.subject || null,
      message: form.value.message,
    })
    sent.value = true
    // ← FIX: thiếu dấu ":" sau subject, và reset đúng keys
    form.value = { name: '', email: '', subject: '', message: '' }
  } catch (e: any) {
    errorMsg.value = e?.response?.data?.message || 'Gửi yêu cầu thất bại'
  } finally {
    loading.value = false
  }
}
</script>
