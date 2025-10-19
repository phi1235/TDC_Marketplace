<template>
  <div class="p-6 max-w-7xl mx-auto">
    <h2 class="text-xl font-bold mb-4">🔍 Tìm kiếm sản phẩm</h2>

    <!-- Ô tìm kiếm -->
    <div class="flex gap-3 mb-6">
      <input
        v-model="keyword"
        type="text"
        placeholder="Nhập từ khóa..."
        class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
      />
      <button
        @click="searchProducts"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition"
      >
        Tìm kiếm
      </button>
    </div>

    <!-- Skeleton loading -->
    <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <div
        v-for="n in 6"
        :key="n"
        class="border rounded-lg shadow-sm p-4 animate-pulse"
      >
        <div class="bg-gray-300 h-40 w-full rounded-lg mb-3"></div>
        <div class="h-4 bg-gray-300 rounded w-3/4 mb-2"></div>
        <div class="h-4 bg-gray-200 rounded w-1/2 mb-2"></div>
        <div class="h-5 bg-gray-400 rounded w-1/3"></div>
      </div>
    </div>

    <!-- Kết quả -->
    <div
      v-else-if="results.length"
      class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 transition-all duration-300"
    >
      <div
        v-for="item in results"
        :key="item._id"
        class="border rounded-lg shadow-sm hover:shadow-md transition p-4"
      >
        <img
          :src="item._source.image || 'https://picsum.photos/300/200'"
          alt="Ảnh sản phẩm"
          class="w-full h-40 object-cover rounded-lg mb-3"
        />
        <h3 class="font-semibold text-lg mb-1">{{ item._source.name }}</h3>
        <p class="text-gray-600 text-sm mb-2 line-clamp-2">{{ item._source.description }}</p>
        <p class="font-bold text-blue-600">{{ formatPrice(item._source.price) }}₫</p>
      </div>
    </div>

    <!-- Không có kết quả -->
    <div v-else class="text-gray-500 mt-4 text-center">
      Không tìm thấy sản phẩm nào.
    </div>
  </div>
</template>
<script setup>
import { ref } from 'vue'
import axios from 'axios'

const keyword = ref('')
const results = ref([])
const loading = ref(false)

const searchProducts = async () => {
  if (!keyword.value.trim()) return

  // 🧹 Xóa kết quả cũ ngay khi bắt đầu tìm
  results.value = []
  loading.value = true

  try {
    const { data } = await axios.get(`http://localhost:8001/api/search-es?q=${keyword.value}`)
    results.value = data.data || []
  } catch (error) {
    console.error('Lỗi khi tìm kiếm:', error)
  } finally {
    loading.value = false
  }
}

const formatPrice = (p) => {
  return Number(p).toLocaleString('vi-VN')
}
</script>
