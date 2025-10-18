<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Danh sách yêu thích</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <div
        v-for="wish in wishes.data"
        :key="wish.id"
        class="bg-white shadow rounded-lg p-4 flex flex-col justify-between hover:shadow-lg transition"
      >
        <div>
          <h3 class="text-lg font-semibold mb-2">Wishlist #{{ wish.id }}</h3>
          <p class="text-gray-500 text-sm">Ngày tạo: {{ formatDate(wish.created_at) }}</p>
          <p class="text-gray-500 text-sm">Cập nhật: {{ formatDate(wish.updated_at) }}</p>
        </div>

        <div class="mt-4 flex justify-end items-center">
          <button
            @click="toggleFavorite(wish)"
            class="flex items-center gap-1 px-3 py-1 border rounded hover:bg-gray-100 transition"
          >
            <span v-if="wish.isFavorited">❤️</span>
            <span v-else>🤍</span>
            {{ wish.favoriteCount }}
          </button>
        </div>
      </div>
    </div>

    <div class="mt-6 flex justify-center space-x-2">
      <button
        v-for="link in wishes.links"
        :key="link.label"
        :disabled="!link.url"
        class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition"
        @click="getWishes(link.url)"
      >
        <span v-html="link.label"></span>
      </button>
    </div>

    <div v-if="wishes.data.length === 0" class="mt-6 text-center text-gray-400">
      Chưa có sản phẩm yêu thích
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'

interface Wish {
  id: number
  created_at: string
  updated_at: string
  isFavorited?: boolean
  favoriteCount?: number
}

interface Pagination {
  data: Wish[]
  links: any[]
}

const wishes = ref<Pagination>({ data: [], links: [] })

// Lấy dữ liệu wishlist
const getWishes = async (url = 'http://localhost:8001/api/wishes') => {
  try {
    const res = await axios.get(url.startsWith('http') ? url : `http://localhost:8001${url}`)
    // Mặc định isFavorited = false, favoriteCount = 0
    wishes.value = {
      ...res.data,
      data: res.data.data.map((w: Wish) => ({ ...w, isFavorited: false, favoriteCount: 0 }))
    }
  } catch (error) {
    console.error('Error fetching wishes:', error)
  }
}

// Toggle yêu thích (demo, không gọi API thực)
const toggleFavorite = (wish: Wish) => {
  wish.isFavorited = !wish.isFavorited
  wish.favoriteCount = (wish.favoriteCount || 0) + (wish.isFavorited ? 1 : -1)
}

// Format ngày
const formatDate = (dateStr: string) => {
  const d = new Date(dateStr)
  return d.toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

onMounted(() => {
  getWishes()
})
</script>

<style scoped>
/* Card hover effect đã dùng Tailwind, không cần CSS thêm */
</style>
