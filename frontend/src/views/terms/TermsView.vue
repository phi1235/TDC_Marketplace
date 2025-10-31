<!-- src/views/TermsView.vue -->
<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 px-4 flex flex-col justify-between">
    <div class="max-w-4xl mx-auto w-full">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">📜 Điều khoản sử dụng</h1>
        <span v-if="version" class="text-xs px-2 py-1 rounded bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
          Version: {{ version }}
        </span>
      </div>

      <!-- Nội dung điều khoản -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-0 overflow-hidden">
        <!-- Thanh mục lục nhanh -->
        <div class="border-b border-gray-200/70 dark:border-gray-700/50 bg-gray-50/60 dark:bg-gray-900/40 p-4">
          <div class="text-xs text-gray-600 dark:text-gray-300">Mục lục nhanh</div>
          <div class="mt-2 flex flex-wrap gap-2">
            <a v-for="(item, idx) in toc" :key="idx" :href="`#s-${idx+1}`" class="text-xs px-2 py-1 rounded bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:underline">
              {{ idx+1 }}. {{ item }}
            </a>
          </div>
        </div>

        <!-- Thân điều khoản (cuộn độc lập) -->
        <div class="p-6 max-h-[60vh] overflow-y-auto space-y-8 text-gray-700 dark:text-gray-300 leading-relaxed">
          <section :id="'s-1'">
            <h2 class="text-2xl font-semibold mb-3 text-gray-900 dark:text-gray-100">1. Giới thiệu</h2>
            <p>
              Chào mừng bạn đến với <strong>TDC Marketplace</strong> — nền tảng giao dịch học liệu và đồ dùng học tập dành cho sinh viên.
              Khi truy cập hoặc sử dụng dịch vụ, bạn đồng ý chịu sự ràng buộc của Điều khoản này. Nếu bạn không đồng ý, vui lòng ngừng sử dụng dịch vụ.
            </p>
          </section>

          <section :id="'s-2'">
            <h2 class="text-2xl font-semibold mb-3 text-gray-900 dark:text-gray-100">2. Đối tượng & Tài khoản</h2>
            <ul class="list-disc pl-6 space-y-2">
              <li>Bạn cam kết cung cấp thông tin chính xác, đầy đủ khi đăng ký và trong suốt quá trình sử dụng.</li>
              <li>Bạn chịu trách nhiệm bảo mật thông tin đăng nhập và mọi hoạt động phát sinh từ tài khoản.</li>
              <li>Chúng tôi có thể tạm khóa hoặc chấm dứt tài khoản nếu phát hiện hành vi gian lận, spam, hoặc vi phạm Điều khoản.</li>
            </ul>
          </section>

          <section :id="'s-3'">
            <h2 class="text-2xl font-semibold mb-3 text-gray-900 dark:text-gray-100">3. Quy tắc nội dung & đăng tin</h2>
            <ul class="list-disc pl-6 space-y-2">
              <li>Nội dung đăng tải phải chính xác, không gây hiểu lầm, không vi phạm pháp luật, thuần phong mỹ tục, hoặc quyền sở hữu trí tuệ.</li>
              <li>Không đăng hàng cấm, nội dung phản cảm, kích động thù hằn, phân biệt đối xử, hay thông tin sai lệch.</li>
              <li>TDC Marketplace có quyền ẩn/sửa/xóa bài đăng vi phạm và áp dụng biện pháp xử lý tài khoản khi cần.</li>
            </ul>
          </section>

          <section :id="'s-4'">
            <h2 class="text-2xl font-semibold mb-3 text-gray-900 dark:text-gray-100">4. Giao dịch & Thanh toán</h2>
            <ul class="list-disc pl-6 space-y-2">
              <li>Giao dịch giữa người mua và người bán do các bên tự thỏa thuận. Nền tảng chỉ hỗ trợ kết nối và các công cụ tiện ích.</li>
              <li>Nếu sử dụng các phương thức thanh toán ký quỹ/escrow (nếu có), vui lòng đọc kỹ hướng dẫn và phí áp dụng.</li>
              <li>Trong mọi trường hợp, hãy ưu tiên giao dịch an toàn và có bằng chứng (tin nhắn, biên nhận).</li>
            </ul>
          </section>

          <section :id="'s-5'">
            <h2 class="text-2xl font-semibold mb-3 text-gray-900 dark:text-gray-100">5. Phí & Thuế</h2>
            <ul class="list-disc pl-6 space-y-2">
              <li>Một số tính năng/dịch vụ có thể thu phí. Mức phí sẽ được công bố rõ trước khi bạn sử dụng.</li>
              <li>Bạn tự chịu trách nhiệm kê khai và nộp các khoản thuế theo quy định pháp luật (nếu có).</li>
            </ul>
          </section>

          <section :id="'s-6'">
            <h2 class="text-2xl font-semibold mb-3 text-gray-900 dark:text-gray-100">6. Quyền riêng tư & Bảo mật</h2>
            <ul class="list-disc pl-6 space-y-2">
              <li>Chúng tôi tôn trọng quyền riêng tư và chỉ xử lý dữ liệu theo mục đích vận hành, cải thiện dịch vụ, và theo quy định pháp luật.</li>
              <li>Không chia sẻ dữ liệu cá nhân cho bên thứ ba nếu không có cơ sở pháp lý hoặc sự đồng ý của bạn.</li>
              <li>Bạn có quyền truy cập/chỉnh sửa/xóa một số dữ liệu cá nhân theo chính sách bảo mật của nền tảng.</li>
            </ul>
          </section>

          <section :id="'s-7'">
            <h2 class="text-2xl font-semibold mb-3 text-gray-900 dark:text-gray-100">7. Sở hữu trí tuệ</h2>
            <ul class="list-disc pl-6 space-y-2">
              <li>Nội dung, thương hiệu, giao diện và mã nguồn thuộc quyền sở hữu của TDC Marketplace hoặc các bên cấp phép liên quan.</li>
              <li>Không được sao chép, sửa đổi, phân phối trái phép bất kỳ thành phần nào khi chưa được phép.</li>
            </ul>
          </section>

          <section :id="'s-8'">
            <h2 class="text-2xl font-semibold mb-3 text-gray-900 dark:text-gray-100">8. Trách nhiệm & Miễn trừ</h2>
            <ul class="list-disc pl-6 space-y-2">
              <li>Nền tảng không chịu trách nhiệm cho tranh chấp phát sinh giữa người dùng với nhau; chúng tôi sẽ hỗ trợ trong khả năng hợp lý.</li>
              <li>Dịch vụ được cung cấp “như hiện có”. Có thể gián đoạn/bảo trì/nâng cấp định kỳ.</li>
            </ul>
          </section>

          <section :id="'s-9'">
            <h2 class="text-2xl font-semibold mb-3 text-gray-900 dark:text-gray-100">9. Sửa đổi Điều khoản</h2>
            <p>
              Chúng tôi có thể cập nhật Điều khoản bất kỳ lúc nào. Khi thay đổi, chúng tôi sẽ cập nhật <em>phiên bản</em> hiện hành.
              Việc bạn tiếp tục sử dụng sau thay đổi đồng nghĩa với việc bạn chấp nhận Điều khoản mới.
            </p>
          </section>

          <section :id="'s-10'">
            <h2 class="text-2xl font-semibold mb-3 text-gray-900 dark:text-gray-100">10. Liên hệ</h2>
            <ul class="list-disc pl-6 space-y-1">
              <li>Email: <a href="mailto:support@tdc-marketplace.vn" class="text-blue-600 dark:text-blue-400 hover:underline">support@tdc-marketplace.vn</a></li>
              <li>Hotline: <span class="font-semibold">0123 456 789</span></li>
              <li>Địa chỉ: Khoa CNTT - Trường Cao Đẳng Công Nghệ Thủ Đức</li>
            </ul>
          </section>
        </div>
      </div>

      <!-- Đồng ý & lưu consent -->
      <div class="max-w-4xl mx-auto w-full mt-8 bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
        <div class="flex items-start gap-3">
          <input id="agree" type="checkbox" v-model="agree" :disabled="consented" class="mt-1 h-4 w-4 cursor-pointer" />
          <label for="agree" class="cursor-pointer text-gray-700 dark:text-gray-300">
            Tôi đã đọc và đồng ý với Điều khoản
            <span v-if="version" class="font-mono text-xs ml-1 px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700">{{ version }}</span>
          </label>
        </div>

        <div class="mt-4 flex items-center gap-3">
          <button
            class="px-4 py-2 rounded bg-blue-600 text-white disabled:opacity-60 disabled:cursor-not-allowed"
            :disabled="!agree || submitting || consented"
            @click="onAccept"
          >
            <span v-if="submitting">Đang ghi nhận…</span>
            <span v-else-if="consented">Đã đồng ý</span>
            <span v-else>Đồng ý</span>
          </button>

          <span v-if="errorMessage" class="text-sm text-red-500">{{ errorMessage }}</span>
          <span v-if="successMessage" class="text-sm text-green-600">{{ successMessage }}</span>
        </div>
      </div>
    </div>

    <footer class="mt-16 text-center text-sm text-gray-500 dark:text-gray-400 py-6">
      © {{ new Date().getFullYear() }} TDC Marketplace — Mọi quyền được bảo lưu.
    </footer>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'
import { useAuthStore } from '@/stores/auth'

// Mục lục
const toc = [
  'Giới thiệu',
  'Đối tượng & Tài khoản',
  'Quy tắc nội dung & đăng tin',
  'Giao dịch & Thanh toán',
  'Phí & Thuế',
  'Quyền riêng tư & Bảo mật',
  'Sở hữu trí tuệ',
  'Trách nhiệm & Miễn trừ',
  'Sửa đổi Điều khoản',
  'Liên hệ'
]

const router = useRouter()
const route = useRoute()
const auth = useAuthStore()

const version = ref('')
const consented = ref(false)
const agree = ref(false)
const submitting = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

onMounted(async () => {
  // 1) Lấy version điều khoản (public)
  try {
    const { data } = await api.get('/legal/terms')
    version.value = data?.version || ''
  } catch {}

  // 2) Nếu đã đăng nhập thì kiểm tra trạng thái consent
  try {
    if (auth.token) {
      const { data } = await api.get('/legal/consent-status')
      consented.value = !!data?.consented
      if (consented.value) agree.value = true
    }
  } catch {}
})

async function onAccept() {
  errorMessage.value = ''
  successMessage.value = ''
  if (!auth.token) {
    errorMessage.value = 'Vui lòng đăng nhập để ghi nhận đồng ý.'
    return
  }
  if (!version.value) {
    errorMessage.value = 'Không xác định phiên bản điều khoản.'
    return
  }
  try {
    submitting.value = true
    await api.post('/legal/consent', { version: version.value, accept: true })
    consented.value = true
    successMessage.value = 'Đã ghi nhận đồng ý điều khoản.'
    const back = (route.query.redirect as string) || '/'
    setTimeout(() => router.push(back), 350)
  } catch (e: any) {
    errorMessage.value = e?.response?.data?.message || 'Ghi nhận consent thất bại.'
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
/* Chữ cách dòng dễ đọc hơn */
p { margin-bottom: 0.5rem; }
</style>
