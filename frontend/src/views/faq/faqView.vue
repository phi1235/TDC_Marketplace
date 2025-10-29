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
      <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 text-center space-y-6 border-t border-gray-200 dark:border-gray-700"
      >
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
          📞 Cần hỗ trợ thêm?
        </h2>
        <p class="text-gray-600 dark:text-gray-400">
          Nếu bạn không tìm thấy câu trả lời phù hợp, hãy gửi yêu cầu hỗ trợ cho chúng tôi.
        </p>

        <form
          @submit.prevent="sendSupportRequest"
          class="max-w-md mx-auto space-y-4 text-left"
        >
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Họ và tên
            </label>
            <input
              v-model="contactForm.name"
              type="text"
              required
              class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Email
            </label>
            <input
              v-model="contactForm.email"
              type="email"
              required
              class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Nội dung cần hỗ trợ
            </label>
            <textarea
              v-model="contactForm.message"
              required
              rows="4"
              class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-2 focus:ring-blue-500"
            ></textarea>
          </div>

          <button
            type="submit"
            class="w-full bg-blue-600 text-white font-semibold py-3 rounded-lg hover:bg-blue-700 transition"
          >
            📧 Gửi yêu cầu
          </button>
        </form>

        <p v-if="formSent" class="text-green-600 dark:text-green-400 font-medium mt-4">
          ✅ Yêu cầu của bạn đã được gửi! Chúng tôi sẽ phản hồi sớm nhất có thể.
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

const activeIndex = ref(null);
const formSent = ref(false);

const faqs = ref([
  {
    question: "Làm sao để đăng tin rao mới?",
    answer:
      "Bạn có thể đăng nhập, sau đó vào trang Dashboard và chọn 'Đăng tin mới'. Điền đầy đủ thông tin rồi nhấn 'Đăng'.",
  },
  {
    question: "Tôi có thể chỉnh sửa tin rao không?",
    answer:
      "Có. Sau khi đăng, bạn có thể vào mục 'Tin của tôi' trong Dashboard để chỉnh sửa thông tin hoặc hình ảnh.",
  },
  {
    question: "Làm sao để bật chế độ tối (Dark Mode)?",
    answer:
      "Bạn có thể bật/tắt Dark Mode bằng nút 🌙 / ☀️ ở góc trên bên phải trang web.",
  },
  {
    question: "Tin rao của tôi bị từ chối, phải làm sao?",
    answer:
      "Nếu tin bị từ chối, bạn có thể xem lý do trong Dashboard và chỉnh sửa lại cho phù hợp với quy định.",
  },
  {
    question: "Tôi muốn liên hệ với quản trị viên?",
    answer:
      "Bạn có thể gửi email đến support@tdc-marketplace.vn hoặc liên hệ qua form hỗ trợ ở bên dưới.",
  },
]);

const contactForm = ref({
  name: "",
  email: "",
  message: "",
});

const toggle = (index) => {
  activeIndex.value = activeIndex.value === index ? null : index;
};

const sendSupportRequest = () => {
  formSent.value = true;
  contactForm.value = { name: "", email: "", message: "" };
  setTimeout(() => (formSent.value = false), 4000);
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
