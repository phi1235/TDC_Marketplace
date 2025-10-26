<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Danh sách Listings</h1>

    <!-- Loading state -->
    <div v-if="loading" class="text-center py-10">Loading...</div>

    <!-- Listings grid -->
    <div v-else-if="listing.data && listing.data.length > 0"
      class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <div v-for="list in listing.data" :key="list.id"
        class="bg-white shadow rounded-lg p-4 flex flex-col justify-between hover:shadow-lg transition">
        <div>
          <h3 class="text-lg font-semibold mb-2">Listing #{{ list.id }}</h3>
          <p class="text-gray-500 text-sm">Ngày tạo: {{ formatDate(list.created_at) }}</p>
          <p class="text-gray-500 text-sm">Cập nhật: {{ formatDate(list.updated_at) }}</p>
        </div>

        <div class="mt-4 flex justify-end items-center">
          <button @click="toggleFavorite(list)"
            class="flex items-center gap-1 px-3 py-1 border rounded hover:bg-gray-100 transition">
            <span v-if="list.isFavorited">❤️</span>
            <span v-else>🤍</span>
            {{ list.favoriteCount }}
          </button>
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else class="text-center text-gray-500 py-10">
      Chưa có listings nào 😢
    </div>

    <!-- Pagination -->
    <div v-if="listing.links && listing.links.length > 0" class="mt-6 flex justify-center space-x-2">
      <button v-for="link in listing.links" :key="link.label" :disabled="!link.url"
        class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition"
        @click="getListings(link.label === 'Next' ? listing.current_page + 1
          : link.label === 'Previous' ? listing.current_page - 1
            : parseInt(link.label))">
        <span v-html="link.label"></span>
      </button>
    </div>
  </div>
</template>


<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { listingsService } from '@/services/listings'

interface Listing {
  id: number
  created_at: string
  updated_at: string
  isFavorited?: boolean
  favoriteCount?: number
}

interface Pagination {
  data: Listing[]
  links: any[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

// Dữ liệu
const listing = ref<Pagination>({
  data: [],
  links: [],
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0
})

const loading = ref(false)

// Format ngày
const formatDate = (dateStr: string) => {
  const d = new Date(dateStr)
  return d.toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

// Toggle yêu thích demo
const toggleFavorite = (item: Listing) => {
  item.isFavorited = !item.isFavorited
  item.favoriteCount = (item.favoriteCount || 0) + (item.isFavorited ? 1 : -1)
}

// Lấy dữ liệu từ API
const getListings = async (page?: number) => {
  loading.value = true
  try {
    const res = await listingsService.getListings({ per_page: 5, page: page || listing.value.current_page })
    listing.value = {
      ...res,
      data: res.data.sort((a, b) => a.id - b.id)
    }
  } finally {
    loading.value = false
  }
}


// Load lần đầu
onMounted(() => getListings())
</script>


<style scoped>
/* Card hover effect đã dùng Tailwind, không cần CSS thêm */
</style>
