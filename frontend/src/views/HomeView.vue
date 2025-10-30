<template>
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
  </div>
</template>

<script setup lang="ts">
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
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
