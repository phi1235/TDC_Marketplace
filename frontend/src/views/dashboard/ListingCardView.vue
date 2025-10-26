<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Danh sách yêu thích</h1>
    <div v-if="listing.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
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
            <span v-if="wish.isFavorited">❤️</span>
            <span v-else>🤍</span>
            {{ list.favoriteCount }}
          </button>
        </div>
      </div>
    </div>

    <div v-else class="text-center text-gray-500 py-10">
      Hiện chưa có sản phẩm yêu thích nào 😢
    </div>

    <!-- <div class="mt-6 flex justify-center space-x-2">
      <button v-for="link in wishes.links" :key="link.label" :disabled="!link.url"
        class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition"
        @click="getWishes(link.url)">
        <span v-html="link.label"></span>
      </button>
    </div> -->

    <div v-if="listing.length === 0" class="mt-6 text-center text-gray-400">
      Chưa có sản phẩm yêu thích
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { listingsService  } from '@/services/listings'

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


//lấy dữ liệu của bảng listing 

const listing = ref([]);
onMounted(async () => {
  const res = await listingsService.getListings()
  console.log('API trả về:', res) 
  
  // Nếu backend trả về kiểu pagination (data là mảng)
  listing.value = res.data
  // Nếu backend trả về mảng trực tiếp → listing.value = res
})


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

onMounted(async () => {
  console.log("🔍 auth_token hiện tại:", localStorage.getItem("auth_token"))
  await getListings()  // ← chờ API trả về
  console.log(localStorage.getItem("auth_token"));
  console.log('✅ Result:', listing.value)

})
</script>

<style scoped>
/* Card hover effect đã dùng Tailwind, không cần CSS thêm */
</style>
