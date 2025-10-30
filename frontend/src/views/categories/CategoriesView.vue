<template>
  <div class="min-h-screen transition-colors duration-300 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <div class="max-w-7xl mx-auto px-4 py-10">
      <!-- 🏷️ Tiêu đề -->
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Danh mục sản phẩm</h1>
        <router-link
          to="/"
          class="text-blue-600 hover:underline text-sm font-medium"
        >
          ← Quay lại Trang chủ
        </router-link>
      </div>

      <!-- 🔄 Skeleton Loading -->
      <div v-if="loading" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
        <div
          v-for="i in 12"
          :key="'cat-skeleton-' + i"
          class="h-32 rounded-lg bg-gray-200 dark:bg-gray-700 animate-pulse"
        ></div>
      </div>

      <!-- 📂 Danh mục thực tế -->
      <div v-else class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
        <div
          v-for="category in categories"
          :key="category.id"
          @click="goToCategory(category.id)"
          class="group bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg border dark:border-gray-700 cursor-pointer transition overflow-hidden"
        >
          <div class="p-6 flex flex-col items-center justify-center text-center space-y-3">
            <div class="text-4xl group-hover:scale-110 transition-transform">
              {{ getCategoryIcon(category.name) }}
            </div>
            <h3 class="font-semibold text-lg group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
              {{ category.name }}
            </h3>
          </div>
        </div>
      </div>

      <!-- 🦶 Footer -->
      <footer class="mt-16 py-6 text-center text-sm text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700">
        © {{ new Date().getFullYear() }} TDC Marketplace — All rights reserved.
      </footer>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();

const categories = ref<any[]>([]);
const loading = ref(true);

onMounted(async () => {
  await fetchCategories();
});

const fetchCategories = async () => {
  try {
    const res = await axios.get("/api/categories");
    categories.value = res.data;
  } catch (err) {
    console.error("Lỗi khi tải danh mục:", err);
  } finally {
    loading.value = false;
  }
};

const goToCategory = (id: number) => {
  router.push(`/category/${id}`);
};

// 🧩 Icon gợi ý cho từng loại danh mục
const getCategoryIcon = (name: string) => {
  const lower = name.toLowerCase();
  if (lower.includes("sách")) return "📚";
  if (lower.includes("điện")) return "💻";
  if (lower.includes("dụng")) return "✏️";
  if (lower.includes("quần")) return "👕";
  if (lower.includes("phụ")) return "🎒";
  if (lower.includes("khác")) return "📦";
  return "🛍️";
};
</script>

<style scoped>
/* Nhẹ nhàng đồng bộ Dark Mode */
body.dark {
  background-color: #111827;
  color: #f9fafb;
}
</style>
