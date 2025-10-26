<template>
  <div
    class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300"
  >
    <div class="max-w-5xl mx-auto py-16 px-4">
      <h1 class="text-4xl font-bold text-center mb-10">❓ Câu hỏi thường gặp (FAQ)</h1>

      <div class="space-y-4">
        <!-- Vòng lặp qua danh sách câu hỏi -->
        <div
          v-for="(item, index) in faqs"
          :key="index"
          class="border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 overflow-hidden shadow-sm"
        >
          <!-- Tiêu đề câu hỏi -->
          <button
            @click="toggleFAQ(index)"
            class="w-full text-left px-6 py-4 flex justify-between items-center focus:outline-none"
          >
            <span class="font-semibold text-lg">{{ item.question }}</span>
            <span
              class="transition-transform duration-300"
              :class="{ 'rotate-180': activeIndex === index }"
            >
              ▼
            </span>
          </button>

          <!-- Nội dung câu trả lời -->
          <transition name="fade">
            <div v-if="activeIndex === index" class="px-6 pb-4 text-gray-700 dark:text-gray-300">
              {{ item.answer }}
            </div>
          </transition>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";

// Danh sách FAQ (có thể sau này lấy từ API)
const faqs = ref([
  {
    question: "Làm thế nào để đăng bài rao?",
    answer:
      "Bạn cần đăng nhập tài khoản sinh viên, sau đó vào mục 'Đăng tin' trên thanh menu và điền đầy đủ thông tin về sản phẩm.",
  },
  {
    question: "Làm sao để chỉnh sửa hoặc xóa tin đã đăng?",
    answer:
      "Bạn vào trang 'Tin của tôi' trong hồ sơ cá nhân, tại đây bạn có thể chỉnh sửa hoặc xóa tin rao bất kỳ.",
  },
  {
    question: "Tôi có thể liên hệ người bán như thế nào?",
    answer:
      "Trong mỗi tin rao có thông tin liên hệ của người bán, bạn có thể nhắn tin trực tiếp hoặc gửi đề nghị mua.",
  },
  {
    question: "TDC Marketplace có thu phí không?",
    answer:
      "Hiện tại nền tảng hoàn toàn miễn phí cho sinh viên. Mọi hoạt động trao đổi, mua bán đều tự thỏa thuận giữa người mua và người bán.",
  },
  {
    question: "Làm sao để bật chế độ tối (Dark Mode)?",
    answer:
      "Ở góc trên cùng của trang, bạn có thể bật hoặc tắt Dark Mode để tùy chỉnh giao diện theo sở thích.",
  },
]);

const activeIndex = ref<number | null>(null);

const toggleFAQ = (index: number) => {
  activeIndex.value = activeIndex.value === index ? null : index;
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
