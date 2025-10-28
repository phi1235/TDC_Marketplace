<template>
  <div class="max-w-7xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">🌟 Tin mới được duyệt</h1>

    <div v-if="loading" class="text-center text-gray-500 italic py-10">
      Đang tải danh sách tin...
    </div>

    <div v-else-if="listings.length === 0" class="text-center py-20 text-gray-500">
      Chưa có tin nào được duyệt hiển thị.
    </div>

    <div
      v-else
      class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-6"
    >
      <div
        v-for="item in listings"
        :key="item.id"
        class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition"
      >
        <img
          v-if="item.images?.length"
          :src="`/storage/${item.images[0].image_path}`"
          :alt="item.title"
          class="w-full h-48 object-cover"
        />
        <div v-else class="h-48 bg-gray-100 flex items-center justify-center text-gray-400">
          Không có ảnh
        </div>

        <div class="p-4">
          <h3 class="font-semibold truncate mb-1">{{ item.title }}</h3>
          <p class="text-blue-600 font-bold mb-2">{{ formatPrice(item.price) }} ₫</p>

          <router-link
            :to="`/listings/${item.id}`"
            class="block text-center bg-blue-600 text-white rounded-md py-2 hover:bg-blue-700 transition"
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

onMounted(loadPublicListings)
</script>
