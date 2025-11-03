<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 px-4 flex flex-col justify-between">
    <div class="max-w-4xl mx-auto w-full">
      <!-- 🧭 Tiêu đề -->
      <h1 class="text-3xl font-bold text-center mb-8 text-gray-900 dark:text-gray-100">
        ❓ Câu hỏi thường gặp (FAQ)
      </h1>

      <!-- 🔹 Danh sách câu hỏi -->
      <div class="space-y-4 mb-16">
        <div
          v-for="(item, index) in faqs"
          :key="index"
          class="bg-white dark:bg-gray-800 rounded-xl shadow-md transition hover:shadow-lg"
        >
          <button
            @click="toggle(index)"
            class="w-full flex justify-between items-center p-5 text-left focus:outline-none"
          >
            <div class="flex items-center space-x-2">
              <span class="text-blue-600 text-lg">💬</span>
              <span class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                {{ item.question }}
              </span>
            </div>
            <span
              class="text-gray-600 dark:text-gray-300 text-xl transition-transform duration-300"
              :class="{ 'rotate-180': activeIndex === index }"
            >
              ▼
            </span>
          </button>

          <transition name="faq-fade">
            <div
              v-if="activeIndex === index"
              class="px-6 pb-5 text-gray-700 dark:text-gray-300 border-t border-gray-200 dark:border-gray-700"
            >
              {{ item.answer }}
            </div>
          </transition>
        </div>
      </div>

      <!-- 💌 Liên hệ hỗ trợ -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 text-center space-y-6 border-t border-gray-200 dark:border-gray-700">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
          📞 Cần hỗ trợ thêm?
        </h2>
        <p class="text-gray-600 dark:text-gray-400">
          Nếu bạn không tìm thấy câu trả lời phù hợp, hãy gửi yêu cầu hỗ trợ cho chúng tôi.
        </p>

        <form @submit.prevent="sendSupportRequest" class="max-w-md mx-auto space-y-4 text-left">
          <!-- Honeypot chống spam -->
          <input v-model="contactForm._hp" type="text" class="hidden" tabindex="-1" autocomplete="off" />

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Họ và tên
            </label>
            <input
              v-model.trim="contactForm.name"
              type="text"
              required
              :disabled="loading"
              class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Email
            </label>
            <input
              v-model.trim="contactForm.email"
              type="email"
              required
              :disabled="loading"
              class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Chủ đề (tuỳ chọn)
            </label>
            <select
              v-model="contactForm.topic"
              :disabled="loading"
              class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-2 focus:ring-blue-500"
            >
              <option value="">-- Chọn chủ đề --</option>
              <option value="listing">Vấn đề về tin rao</option>
              <option value="account">Tài khoản & đăng nhập</option>
              <option value="payment">Thanh toán/Đơn hàng</option>
              <option value="other">Khác</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Nội dung cần hỗ trợ
            </label>
            <textarea
              v-model.trim="contactForm.message"
              required
              rows="4"
              :disabled="loading"
              class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-2 focus:ring-blue-500"
            ></textarea>
            <p class="mt-1 text-xs text-gray-500">Tối thiểu 10 ký tự.</p>
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full bg-blue-600 text-white font-semibold py-3 rounded-lg hover:bg-blue-700 transition disabled:opacity-60 flex items-center justify-center gap-2"
          >
            <svg v-if="loading" class="animate-spin h-4 w-4" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
            </svg>
            <span>{{ loading ? 'Đang gửi...' : '📧 Gửi yêu cầu' }}</span>
          </button>
        </form>

        <p v-if="formSent" class="text-green-600 dark:text-green-400 font-medium mt-4">
          ✅ Yêu cầu của bạn đã được gửi! Chúng tôi sẽ phản hồi sớm nhất có thể.
        </p>
        <p v-if="errorText" class="text-red-600 dark:text-red-400 font-medium mt-4">
          ❌ {{ errorText }}
        </p>
      </div>
    </div>

    <!-- 🦶 Footer -->
    <footer class="mt-16 text-center text-sm text-gray-500 dark:text-gray-400 py-6">
      © {{ new Date().getFullYear() }} TDC Marketplace — Mọi quyền được bảo lưu.
    </footer>
  </div>
</template>

<script setup>
import { ref } from "vue";
import api from "@/services/api"; // axios instance của bạn

const activeIndex = ref(null);
const formSent = ref(false);
const loading = ref(false);
const errorText = ref("");

const faqs = ref([
  { question: "Làm sao để đăng tin rao mới?", answer: "Đăng nhập → chọn 'Đăng tin rao' → điền thông tin → 'Đăng'." },
  { question: "Tôi có thể chỉnh sửa tin rao không?", answer: "Vào 'Tin của tôi' để sửa nội dung hoặc hình ảnh." },
  { question: "Làm sao bật chế độ tối (Dark Mode)?", answer: "Bật/tắt ở nút 🌙/☀️ trên giao diện." },
  { question: "Tin rao bị từ chối thì sao?", answer: "Xem lý do trong Dashboard, sửa lại cho phù hợp quy định." },
  { question: "Liên hệ quản trị viên?", answer: "Gửi form hỗ trợ bên dưới hoặc email support@tdc-marketplace.vn." },
]);

const contactForm = ref({
  name: "",
  email: "",
  topic: "",
  message: "",
  _hp: "", // honeypot
});

const toggle = (index) => {
  activeIndex.value = activeIndex.value === index ? null : index;
};

const sendSupportRequest = async () => {
  errorText.value = "";
  formSent.value = false;

  if (contactForm.value._hp) return; // spam bot

  if (!contactForm.value.message || contactForm.value.message.length < 10) {
    errorText.value = "Nội dung hỗ trợ tối thiểu 10 ký tự.";
    return;
  }

  try {
    loading.value = true;
    await api.post("/support/contact", {
      name: contactForm.value.name,
      email: contactForm.value.email,
      topic: contactForm.value.topic || null,
      message: contactForm.value.message,
    });

    formSent.value = true;
    contactForm.value = { name: "", email: "", topic: "", message: "", _hp: "" };
    setTimeout(() => (formSent.value = false), 5000);
  } catch (err) {
    errorText.value =
      err?.response?.data?.message || "Không gửi được yêu cầu. Vui lòng thử lại.";
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.faq-fade-enter-active,
.faq-fade-leave-active {
  transition: all 0.3s ease;
}
.faq-fade-enter-from,
.faq-fade-leave-to {
  opacity: 0;
  transform: translateY(-5px);
}
</style>
