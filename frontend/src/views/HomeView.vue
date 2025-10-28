<template>
<<<<<<< HEAD
  <div class="min-h-screen transition-colors duration-300 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    
    <!-- 🖼 Banner -->
    <section class="relative bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-16 px-4 text-center rounded-lg shadow-md mb-10">
      <h1 class="text-4xl md:text-5xl font-bold mb-4">Chào mừng đến với TDC Marketplace 🎓</h1>
      <p class="text-lg mb-8 opacity-90">Nền tảng trao đổi đồ học tập dành cho sinh viên</p>
      <router-link
        to="/listings"
        class="bg-white text-blue-600 font-semibold px-6 py-3 rounded-lg shadow hover:bg-gray-100 transition-colors"
      >
        Khám phá ngay
      </router-link>
    </section>

    <div class="max-w-7xl mx-auto px-4">
      <!-- 🧭 Danh mục -->
      <section class="mb-12">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-semibold">Danh mục phổ biến</h2>
          <router-link
            to="/categories"
            class="text-blue-600 hover:underline text-sm font-medium"
          >
            Xem tất cả →
          </router-link>
        </div>

        <!-- Skeleton Loading cho danh mục -->
        <div v-if="loadingCategories" class="grid grid-cols-2 md:grid-cols-6 gap-4">
          <div
            v-for="i in 6"
            :key="'cat-skeleton-' + i"
            class="h-24 rounded-lg bg-gray-200 dark:bg-gray-700 animate-pulse"
          ></div>
        </div>

        <!-- Danh mục thực tế -->
        <div v-else class="grid grid-cols-2 md:grid-cols-6 gap-4">
          <div
            v-for="cat in categories"
            :key="cat.id"
            class="p-4 border rounded-lg bg-white dark:bg-gray-800 hover:shadow-md cursor-pointer transition"
            @click="goToCategory(cat.id)"
          >
            <div class="text-center space-y-2">
              <div class="text-3xl">📦</div>
              <div class="font-medium">{{ cat.name }}</div>
            </div>
          </div>
        </div>
      </section>

      <!-- 🛍 Tin rao mới nhất -->
      <section>
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-semibold">Tin rao mới nhất</h2>
          <router-link
            to="/listings"
            class="text-blue-600 hover:underline text-sm font-medium"
          >
            Xem tất cả →
          </router-link>
        </div>

        <!-- Skeleton Loading -->
        <div v-if="loadingListings" class="grid grid-cols-2 md:grid-cols-4 gap-6">
          <div
            v-for="i in 8"
            :key="'list-skeleton-' + i"
            class="rounded-xl bg-gray-200 dark:bg-gray-700 h-64 animate-pulse"
          ></div>
        </div>

        <!-- Danh sách tin rao -->
        <div v-else class="grid grid-cols-2 md:grid-cols-4 gap-6">
          <div
            v-for="listing in listings"
            :key="listing.id"
            class="relative bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-xl overflow-hidden hover:shadow-lg transition"
          >
            <!-- ❤️ Trái tim -->
            <button
              class="absolute top-3 right-3 bg-white dark:bg-gray-700 rounded-full p-2 hover:scale-110 transition"
            >
              ❤️
            </button>

            <img
              :src="listing.image_url || '/placeholder.jpg'"
              alt="listing"
              class="w-full h-48 object-cover"
            />

            <div class="p-4 space-y-2">
              <h3 class="font-semibold text-lg line-clamp-1">{{ listing.title }}</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                {{ listing.description }}
              </p>
              <div class="flex justify-between items-center mt-2">
                <span class="font-bold text-blue-600">{{ formatPrice(listing.price) }}</span>
                <router-link
                  :to="`/listing/${listing.id}`"
                  class="text-sm text-blue-500 hover:underline"
                >
                  Xem chi tiết
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- 🦶 Footer -->
    <footer class="mt-16 py-6 bg-gray-100 dark:bg-gray-800 text-center text-sm text-gray-600 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700">
      © {{ new Date().getFullYear() }} TDC Marketplace — All rights reserved.
    </footer>
=======
  <div class="max-w-7xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">
      🌟 Tin mới được duyệt
    </h1>

    <!-- Loading -->
    <div v-if="loading" class="text-center text-gray-500 italic py-10">
      Đang tải danh sách tin...
    </div>

    <!-- Empty -->
    <div v-else-if="listings.length === 0" class="text-center py-20 text-gray-500">
      Chưa có tin nào được duyệt hiển thị.
    </div>

    <!-- Listings -->
    <div
      v-else
      class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-6"
    >
      <div
        v-for="item in listings"
        :key="item.id"
        class="group bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1"
      >
        <!-- Ảnh -->
        <div class="relative overflow-hidden">
          <img
            v-if="item.images?.length"
            :src="imageUrl(item.images[0].image_path)"
            :alt="item.title"
            class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-105"
          />
          <div
            v-else
            class="h-48 bg-gray-100 flex items-center justify-center text-gray-400"
          >
            Không có ảnh
          </div>
          <div
            class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"
          ></div>
        </div>

        <!-- Nội dung -->
        <div class="p-4">
          <h3
            class="text-lg font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors"
          >
            {{ item.title }}
          </h3>

          <!-- Giá -->
          <p class="text-xl font-bold text-blue-600 mb-2">
            {{ formatPrice(item.price) }} VNĐ
          </p>

          <!-- Tình trạng -->
          <div class="flex items-center mb-2">
            <span class="text-sm text-gray-600 dark:text-gray-300">Tình trạng:</span>
            <span
              :class="getConditionClass(item.condition)"
              class="ml-2 px-2 py-1 rounded-full text-xs font-medium"
            >
              {{ getConditionText(item.condition) }}
            </span>
          </div>

          <!-- Lượt xem -->
          <div class="flex items-center text-sm text-gray-600 mb-4">
            <svg
              class="h-4 w-4 mr-1"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
              ></path>
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
              ></path>
            </svg>
            <span>{{ item.views_count }} lượt xem</span>
          </div>

          <!-- Nút -->
          <router-link
            :to="`/listings/${item.id}`"
            class="block text-center bg-blue-600 text-white rounded-md py-2 font-medium hover:bg-blue-700 active:scale-95 transition-transform"
          >
            Xem chi tiết
          </router-link>
        </div>
      </div>
    </div>
>>>>>>> main
  </div>
</template>

<script setup lang="ts">
<<<<<<< HEAD
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const router = useRouter();

const categories = ref([]);
const listings = ref([]);
const loadingCategories = ref(true);
const loadingListings = ref(true);

onMounted(async () => {
  await fetchCategories();
  await fetchListings();
});

const fetchCategories = async () => {
  try {
    const res = await axios.get("/api/categories");
    categories.value = res.data.slice(0, 6);
  } catch (err) {
    console.error("Lỗi tải danh mục:", err);
  } finally {
    loadingCategories.value = false;
  }
};

// ✅ Lấy tin rao mới nhất — không phân trang
const fetchListings = async () => {
  try {
    loadingListings.value = true;
    const res = await axios.get("/api/listings/latest");
    listings.value = res.data;
  } catch (err) {
    console.error("Lỗi tải tin rao mới nhất:", err);
  } finally {
    loadingListings.value = false;
  }
};

const goToCategory = (id: number) => {
  router.push(`/category/${id}`);
};

const formatPrice = (price: number) => {
  if (!price) return "Liên hệ";
  return price.toLocaleString("vi-VN", { style: "currency", currency: "VND" });
};
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
=======
import { ref, onMounted } from 'vue'
import { listingsService } from '@/services/listings'
import { showToast } from '@/utils/toast'
import { imageUrl } from '@/utils/image'

const listings = ref([])
const loading = ref(false)

const loadPublicListings = async () => {
  loading.value = true
  try {
    const res = await listingsService.getPublicListings()
    listings.value = res.data
  } catch (error) {
    console.error(error)
    showToast('error', 'Không thể tải danh sách tin')
  } finally {
    loading.value = false
  }
}

const formatPrice = (price: number) => new Intl.NumberFormat('vi-VN').format(price)

// Class tình trạng
const getConditionClass = (condition: string) => {
  const classes: Record<string, string> = {
    new: 'bg-green-100 text-green-800',
    like_new: 'bg-blue-100 text-blue-800',
    good: 'bg-yellow-100 text-yellow-800',
    fair: 'bg-red-100 text-red-800'
  }
  return classes[condition] || 'bg-gray-100 text-gray-800'
}

// Text tình trạng
const getConditionText = (condition: string) => {
  const texts: Record<string, string> = {
    new: 'Mới',
    like_new: 'Như mới',
    good: 'Tốt',
    fair: 'Khá'
  }
  return texts[condition] || 'Không xác định'
}

onMounted(loadPublicListings)
</script>

<style scoped>
>>>>>>> main
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
